<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\DiscountProduct;
use App\Models\Product;

class GetDiscountService
{

    public function getDiscount(int $productId): array
    {
        $product = Product::find($productId);
        if (!$product) {
            return [
                'status' => 'error',
                'data' => [],
                'message' => 'محصول پیدا نشد',
            ];
        }

        $candidates = [
            ['id' => $product->id, 'type' => 'amazingsale'],
            ['id' => $product->id, 'type' => 'common'],
            ['id' => $product->group_id_in_app, 'type' => 'common'],
            ['id' => $product->main_group_id_in_app, 'type' => 'common'],
        ];

        foreach ($candidates as $item) {
            if (!$item['id']) continue;

            $discountIds = DiscountProduct::where('target_id', $item['id'])
                ->whereNull('deleted_at')
                ->pluck('discount_id');

            if ($discountIds->isEmpty()) continue;

            $discount = Discount::whereIn('id', $discountIds)
                ->active()
                ->where('discount_type', $item['type'])
                ->orderByDesc('created_at')
                ->first();

            if ($discount) {
                $pivot = DiscountProduct::where('discount_id', $discount->id)
                    ->where('target_id', $item['id'])
                    ->whereNull('deleted_at')
                    ->first();

                $percentage = $pivot ? (float)$pivot->percentage : 0;

                return [
                    'status' => 'success',
                    'data' => [
                        'percentage' => $percentage,
                        'type' => $discount->discount_type,
                        'discount_type_fa' => $discount->discount_type_fa,
                    ],
                    'message' => 'تخفیف یافت شد',
                ];
            }
        }

        return [
            'status' => 'error',
            'data' => [],
            'message' => 'تخفیفی برای این محصول موجود نیست',
        ];
    }
}
