<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProvinceCity;
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
            ->where('orders.status', 1)
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
            'items',
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
            'new_status' => ['required', 'integer', 'in:1,2,3,4'],
            'user_id' => ['required', 'integer'],
            'user_name' => ['required', 'string', 'max:255'],
        ]);

        $statusTitles = [
            1 => 'پرداخت شده',
            2 => 'تأیید پرداخت',
            3 => 'صدور فاکتور',
            4 => 'ارسال بار',
        ];


        DB::beginTransaction();

        try {
            $order = Order::query()
                ->findOrFail($validated['order_id']);

            $order->update([
                'status' => $validated['new_status'],
                'status_title' => $statusTitles[$validated['new_status']],
            ]);

            _log($order->id, 'status-' . $validated['new_status'], 'order', 'payment-check', $statusTitles[$validated['new_status']], $validated['user_id'], $validated['user_name']);
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

}
