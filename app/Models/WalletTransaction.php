<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    public const REASON_ORDER_PAY = 'order_pay';
    public const REASON_ORDER_PAY_RELEASE = 'order_pay_release';
    public const REASON_ORDER_CANCEL = 'order_cancel';

    public const STATUS_PENDING_FINANCE = 1;
    public const STATUS_FINANCE_CONFIRMED = 2;

    public const REASON_TITLES = [
        self::REASON_ORDER_PAY => 'کسر بابت پرداخت سفارش',
        self::REASON_ORDER_PAY_RELEASE => 'بازگشت موجودی رزرو پرداخت',
        self::REASON_ORDER_CANCEL => 'واریز بابت لغو سفارش',
    ];

    public const STATUS_TITLES = [
        self::STATUS_PENDING_FINANCE => 'منتظر تایید مالی',
        self::STATUS_FINANCE_CONFIRMED => 'تایید مالی',
    ];

    protected $fillable = [
        'wallet_id',
        'user_id',
        'order_id',
        'type',
        'reason',
        'status',
        'amount',
        'balance_after',
        'description',
        'idempotency_key',
    ];

    protected $casts = [
        'amount' => 'integer',
        'status' => 'integer',
        'balance_after' => 'integer',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function resolvedReasonTitle(): string
    {
        return self::REASON_TITLES[$this->reason] ?? $this->reason;
    }

    public static function titleForStatus(int $status): string
    {
        return self::STATUS_TITLES[$status] ?? 'نامشخص';
    }

    public function resolvedStatusTitle(): string
    {
        return self::titleForStatus((int) $this->status);
    }

    public function isCredit(): bool
    {
        return $this->type === self::TYPE_CREDIT;
    }

    public function isPendingFinance(): bool
    {
        return (int) $this->status === self::STATUS_PENDING_FINANCE;
    }

    public function isFinanceConfirmed(): bool
    {
        return (int) $this->status === self::STATUS_FINANCE_CONFIRMED;
    }
}
