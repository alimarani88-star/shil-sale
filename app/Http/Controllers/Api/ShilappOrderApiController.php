<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProvinceCity;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShilappOrderApiController extends Controller
{
    public function orders(): JsonResponse
    {
        $paymentLogs = Log::query()
            ->selectRaw('process_id, MAX(created_at) AS payment_time')
            ->where('table_name', 'orders')
            ->where('action', 'status-1')
            ->groupBy('process_id');

        $orders = Order::query()
            ->leftJoinSub(
                $paymentLogs,
                'payment_logs',
                fn ($join) => $join->on(
                    'payment_logs.process_id',
                    '=',
                    'orders.id'
                )
            )
            ->where('orders.status', Order::STATUS_PAID)
            ->select([
                'orders.*',
                'payment_logs.payment_time',
            ])
            ->latest('orders.id')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
            'message' => '',
        ]);
    }

    public function orders_all(): JsonResponse
    {
        $paymentLogs = Log::query()
            ->selectRaw('process_id, MAX(created_at) AS payment_time')
            ->where('table_name', 'orders')
            ->where('action', 'status-1')
            ->groupBy('process_id');

        $orders = Order::query()
            ->leftJoinSub(
                $paymentLogs,
                'payment_logs',
                fn ($join) => $join->on(
                    'payment_logs.process_id',
                    '=',
                    'orders.id'
                )
            )
            ->select([
                'orders.*',
                'payment_logs.payment_time',
            ])
            ->latest('orders.id')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
            'message' => '',
        ]);
    }

    public function order(int $orderId): JsonResponse
    {
        $order = Order::with([
            'items.product:id,product_id_in_app',
            'logs',
        ])->find($orderId);
        if (!$order) {
            return response()->json([
                'status'  => 'error',
                'data'    => null,
                'message' => 'سفارش موردنظر یافت نشد.',
            ]);
        }
        $order->address = Address::find($order->address_id);
        if ($order->address) {
            $province = ProvinceCity::find($order->address->province_id);
            $city = ProvinceCity::find($order->address->city_id);

            $order->address->province = $province?->title;
            $order->address->city = $city?->title;
        }
        return response()->json([
            'status'  => 'success',
            'data'    => $order,
            'message' => '',
        ]);
    }

    public function order_update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'new_status' => ['required', 'integer', 'in:' . implode(',', Order::shilappAssignableStatuses())],
            'user_id' => ['required', 'integer'],
            'user_name' => ['required', 'string', 'max:255'],
            'RK_invoice_number' => ['required_if:new_status,' . Order::STATUS_INVOICED, 'nullable', 'string', 'max:100'],
        ]);

        $statusTitle = Order::titleForStatus((int) $validated['new_status']);

        DB::beginTransaction();

        try {
            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($validated['order_id']);

            if ($order->isCancelledToWallet()) {
                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'این سفارش لغو شده و قابل به‌روزرسانی نیست.',
                ]);
            }

            $updateData = [
                'status' => $validated['new_status'],
                'status_title' => $statusTitle,
            ];

            if (!empty($validated['RK_invoice_number'])) {
                $updateData['RK_invoice_number'] = trim($validated['RK_invoice_number']);
            }

            $order->update($updateData);

            _log($order->id, 'status-' . $validated['new_status'], 'order', 'payment-check', $statusTitle, $validated['user_id'], $validated['user_name']);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $order->refresh(),
                'message' => 'وضعیت سفارش با موفقیت تغییر کرد.',
            ]);

        } catch (\Throwable $exception) {
            DB::rollBack();

            report($exception);

            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => 'خطایی در به‌روزرسانی سفارش رخ داد.',
            ]);
        }
    }

    public function wallet_pending(): JsonResponse
    {
        $credits = WalletTransaction::query()
            ->with(['order:id,code,customer_id,customer_name,total_price'])
            ->where('type', WalletTransaction::TYPE_CREDIT)
            ->where('reason', WalletTransaction::REASON_ORDER_CANCEL)
            ->where('status', WalletTransaction::STATUS_PENDING_FINANCE)
            ->latest('id')
            ->get()
            ->map(function (WalletTransaction $transaction) {
                return [
                    'id' => $transaction->id,
                    'order_id' => $transaction->order_id,
                    'order_code' => $transaction->order?->code,
                    'customer_id' => $transaction->user_id,
                    'customer_name' => $transaction->order?->customer_name,
                    'amount' => (int) $transaction->amount,
                    'status' => (int) $transaction->status,
                    'status_title' => $transaction->resolvedStatusTitle(),
                    'created_at' => optional($transaction->created_at)?->toDateTimeString(),
                ];
            })
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $credits,
            'message' => '',
        ]);
    }

    public function wallet_confirm(Request $request, WalletService $walletService): JsonResponse
    {
        $validated = $request->validate([
            'transaction_id' => ['required', 'integer', 'exists:wallet_transactions,id'],
            'user_id' => ['required', 'integer'],
            'user_name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $transaction = $walletService->confirmFinance((int) $validated['transaction_id']);

            _log(
                $transaction->id,
                'wallet-status-' . WalletTransaction::STATUS_FINANCE_CONFIRMED,
                'wallet_transactions',
                'payment',
                WalletTransaction::titleForStatus(WalletTransaction::STATUS_FINANCE_CONFIRMED),
                $validated['user_id'],
                $validated['user_name']
            );

            return response()->json([
                'status' => 'success',
                'data' => $transaction,
                'message' => 'مبلغ کیف پول با موفقیت تأیید مالی شد.',
            ]);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => 'خطایی در تأیید مالی کیف پول رخ داد.',
            ]);
        }
    }

}
