<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    public function getBalance(int $userId): int
    {
        return (int) (Wallet::query()->where('user_id', $userId)->value('balance') ?? 0);
    }

    public function getPendingBalance(int $userId): int
    {
        return (int) (Wallet::query()->where('user_id', $userId)->value('pending_balance') ?? 0);
    }

    public function previewWalletUse(int $userId, int $amount): int
    {
        if ($amount <= 0) {
            return 0;
        }

        return min($this->getBalance($userId), $amount);
    }

    /**
     * موجودی کیف پول را برای سفارش در انتظار پرداخت رزرو می‌کند و مبلغ باقیماندهٔ درگاه را برمی‌گرداند.
     */
    public function applyPaymentHoldForPendingOrder(Order $order): int
    {
        return (int) DB::transaction(function () use ($order) {
            $lockedOrder = $this->lockedOrder((int) $order->id);
            if (!$lockedOrder->isPendingPayment()) {
                throw new RuntimeException('این سفارش قابل پرداخت نیست.');
            }

            $total = $this->amountFromOrder($lockedOrder);
            $wallet = $this->lockedWallet((int) $lockedOrder->customer_id);
            $netHold = $this->netPayHold($wallet->id, $lockedOrder->id);
            $desired = min($wallet->balance + $netHold, $total);

            $this->syncPayHold($wallet, $lockedOrder, $desired, false);

            $lockedOrder->update([
                'wallet_used_amount' => $desired,
            ]);

            return $total - $desired;
        });
    }

    /**
     * رزرو پرداخت سفارش را آزاد می‌کند (انقضا یا ساخت دوبارهٔ سفارش در انتظار).
     */
    public function releasePaymentHold(Order $order, bool $resetWalletUsedAmount = false): void
    {
        DB::transaction(function () use ($order, $resetWalletUsedAmount) {
            $lockedOrder = $this->lockedOrder((int) $order->id);
            $wallet = Wallet::query()
                ->where('user_id', (int) $lockedOrder->customer_id)
                ->lockForUpdate()
                ->first();

            if ($wallet) {
                $this->syncPayHold($wallet, $lockedOrder, 0, false);
            }

            if ($resetWalletUsedAmount && (int) $lockedOrder->wallet_used_amount !== 0) {
                $lockedOrder->update([
                    'wallet_used_amount' => 0,
                ]);
            }
        });
    }

    /**
     * بعد از پرداخت موفق، رزرو ذخیره‌شده روی سفارش را اعمال می‌کند (مثلاً پرداخت دیرهنگام پس از انقضا).
     */
    public function ensurePaymentHold(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = $this->lockedOrder((int) $order->id);
            $desired = (int) $lockedOrder->wallet_used_amount;
            if ($desired <= 0) {
                return;
            }

            $wallet = $this->lockedWallet((int) $lockedOrder->customer_id);
            $this->syncPayHold($wallet, $lockedOrder, $desired, true);
        });
    }

    /**
     * سفارش پرداخت‌شده را قبل از صدور فاکتور لغو می‌کند و مبلغ را به کیف پول واریز می‌کند.
     */
    public function creditCancelledOrder(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $lockedOrder = $this->lockedOrder((int) $order->id);

            if ($lockedOrder->isCancelledToWallet()) {
                throw new RuntimeException('این سفارش قبلاً لغو شده است.');
            }

            if (!$lockedOrder->canBeCancelledByCustomer()) {
                throw new RuntimeException('امکان لغو این سفارش وجود ندارد.');
            }

            $amount = $this->amountFromOrder($lockedOrder);
            $cancelKey = 'order_cancel:' . $lockedOrder->id;

            if (WalletTransaction::query()->where('idempotency_key', $cancelKey)->exists()) {
                throw new RuntimeException('مبلغ این سفارش قبلاً به کیف پول واریز شده است.');
            }

            if ($amount > 0) {
                $financeStatus = (int) $lockedOrder->status === Order::STATUS_PAYMENT_CONFIRMED
                    ? WalletTransaction::STATUS_FINANCE_CONFIRMED
                    : WalletTransaction::STATUS_PENDING_FINANCE;

                $wallet = $this->lockedWallet((int) $lockedOrder->customer_id);
                $this->credit(
                    $wallet,
                    $amount,
                    WalletTransaction::REASON_ORDER_CANCEL,
                    $lockedOrder->id,
                    'واریز مبلغ سفارش ' . $lockedOrder->code . ' پس از لغو',
                    $cancelKey,
                    $financeStatus
                );
            }

            $status = Order::STATUS_CANCELLED_TO_WALLET;
            $lockedOrder->update([
                'status' => $status,
                'status_title' => Order::titleForStatus($status),
            ]);

            return $lockedOrder->refresh();
        });
    }

    /**
     * تایید مالی مبلغ واریزی لغو سفارش که هنوز تایید نشده است.
     */
    public function confirmFinance(int $transactionId): WalletTransaction
    {
        return DB::transaction(function () use ($transactionId) {
            $transaction = WalletTransaction::query()
                ->where('id', $transactionId)
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                throw new RuntimeException('تراکنش کیف پول پیدا نشد.');
            }

            if (
                $transaction->type !== WalletTransaction::TYPE_CREDIT
                || $transaction->reason !== WalletTransaction::REASON_ORDER_CANCEL
            ) {
                throw new RuntimeException('این تراکنش قابل تأیید مالی نیست.');
            }

            if ($transaction->isFinanceConfirmed()) {
                throw new RuntimeException('این مبلغ قبلاً تأیید مالی شده است.');
            }

            if (!$transaction->isPendingFinance()) {
                throw new RuntimeException('وضعیت این تراکنش برای تأیید مالی معتبر نیست.');
            }

            $wallet = Wallet::query()
                ->where('id', $transaction->wallet_id)
                ->lockForUpdate()
                ->first();

            if (!$wallet) {
                throw new RuntimeException('کیف پول مربوط به این تراکنش پیدا نشد.');
            }

            $amount = (int) $transaction->amount;
            if ((int) $wallet->pending_balance < $amount) {
                throw new RuntimeException('موجودی در انتظار تأیید برای این مبلغ کافی نیست.');
            }

            $wallet->pending_balance = (int) $wallet->pending_balance - $amount;
            $wallet->balance = (int) $wallet->balance + $amount;
            $wallet->save();

            $transaction->update([
                'status' => WalletTransaction::STATUS_FINANCE_CONFIRMED,
                'balance_after' => (int) $wallet->balance,
            ]);

            return $transaction->refresh();
        });
    }

    private function syncPayHold(Wallet $wallet, Order $order, int $desired, bool $allowPartial): void
    {
        $desired = max(0, $desired);
        $netHold = $this->netPayHold($wallet->id, $order->id);

        if ($netHold === $desired) {
            return;
        }

        if ($desired > $netHold) {
            $need = $desired - $netHold;
            if ($wallet->balance < $need) {
                if (!$allowPartial || $wallet->balance <= 0) {
                    if (!$allowPartial) {
                        throw new RuntimeException('موجودی کیف پول برای این پرداخت کافی نیست.');
                    }

                    return;
                }

                $need = $wallet->balance;
            }

            $this->debit(
                $wallet,
                $need,
                WalletTransaction::REASON_ORDER_PAY,
                $order->id,
                'کسر از کیف پول بابت سفارش ' . $order->code
            );

            return;
        }

        $this->credit(
            $wallet,
            $netHold - $desired,
            WalletTransaction::REASON_ORDER_PAY_RELEASE,
            $order->id,
            'بازگشت موجودی رزرو سفارش ' . $order->code
        );
    }

    private function netPayHold(int $walletId, int $orderId): int
    {
        $debits = (int) WalletTransaction::query()
            ->where('wallet_id', $walletId)
            ->where('order_id', $orderId)
            ->where('type', WalletTransaction::TYPE_DEBIT)
            ->where('reason', WalletTransaction::REASON_ORDER_PAY)
            ->sum('amount');

        $credits = (int) WalletTransaction::query()
            ->where('wallet_id', $walletId)
            ->where('order_id', $orderId)
            ->where('type', WalletTransaction::TYPE_CREDIT)
            ->where('reason', WalletTransaction::REASON_ORDER_PAY_RELEASE)
            ->sum('amount');

        return max(0, $debits - $credits);
    }

    private function credit(
        Wallet $wallet,
        int $amount,
        string $reason,
        ?int $orderId,
        string $description,
        ?string $idempotencyKey = null,
        int $financeStatus = WalletTransaction::STATUS_FINANCE_CONFIRMED
    ): void {
        if ($amount <= 0) {
            return;
        }

        if ($financeStatus === WalletTransaction::STATUS_PENDING_FINANCE) {
            $wallet->pending_balance = (int) $wallet->pending_balance + $amount;
        } else {
            $wallet->balance = (int) $wallet->balance + $amount;
        }
        $wallet->save();

        $this->writeTransaction(
            $wallet,
            WalletTransaction::TYPE_CREDIT,
            $reason,
            $amount,
            $orderId,
            $description,
            $idempotencyKey,
            $financeStatus
        );
    }

    private function debit(
        Wallet $wallet,
        int $amount,
        string $reason,
        ?int $orderId,
        string $description
    ): void {
        if ($amount <= 0) {
            return;
        }

        if ((int) $wallet->balance < $amount) {
            throw new RuntimeException('موجودی کیف پول کافی نیست.');
        }

        $wallet->balance = (int) $wallet->balance - $amount;
        $wallet->save();

        $this->writeTransaction(
            $wallet,
            WalletTransaction::TYPE_DEBIT,
            $reason,
            $amount,
            $orderId,
            $description,
            null,
            WalletTransaction::STATUS_FINANCE_CONFIRMED
        );
    }

    private function writeTransaction(
        Wallet $wallet,
        string $type,
        string $reason,
        int $amount,
        ?int $orderId,
        string $description,
        ?string $idempotencyKey,
        int $financeStatus = WalletTransaction::STATUS_FINANCE_CONFIRMED
    ): void {
        WalletTransaction::query()->create([
            'wallet_id' => $wallet->id,
            'user_id' => $wallet->user_id,
            'order_id' => $orderId,
            'type' => $type,
            'reason' => $reason,
            'status' => $financeStatus,
            'amount' => $amount,
            'balance_after' => (int) $wallet->balance,
            'description' => $description,
            'idempotency_key' => $idempotencyKey,
        ]);
    }

    private function lockedOrder(int $orderId): Order
    {
        $order = Order::query()->where('id', $orderId)->lockForUpdate()->first();
        if (!$order) {
            throw new RuntimeException('سفارش موردنظر پیدا نشد.');
        }

        return $order;
    }

    private function lockedWallet(int $userId): Wallet
    {
        $wallet = Wallet::query()->where('user_id', $userId)->lockForUpdate()->first();
        if ($wallet) {
            return $wallet;
        }

        try {
            Wallet::query()->create([
                'user_id' => $userId,
                'balance' => 0,
                'pending_balance' => 0,
            ]);
        } catch (QueryException $exception) {
            // ایجاد همزمان کیف پول برای یک کاربر
        }

        $wallet = Wallet::query()->where('user_id', $userId)->lockForUpdate()->first();
        if (!$wallet) {
            throw new RuntimeException('ایجاد کیف پول ممکن نشد.');
        }

        return $wallet;
    }

    public function amountFromOrder(Order $order): int
    {
        return (int) round((float) $order->total_price);
    }
}
