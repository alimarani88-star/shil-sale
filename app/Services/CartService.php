<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class CartService
{
    public function __construct(private readonly GetDiscountService $getDiscountService)
    {
    }


    public function getPriceSendData(?Address $address = null)
    {
        $addresses = Address::where('user_id', Auth::id())
            ->with(['province', 'city'])
            ->get();

        $default_address = $address ?? $addresses->where('is_default', 1)->first();

        $carts = Cart::where('user_id', Auth::id())
            ->with('product.images')
            ->get();

        $price_send = $this->CalculatePriceSend($default_address, $carts);

        if (isset($price_send['error'])) {
            return ['error' => $price_send['error']];
        }

        [$totalPrice, $amountPayable, $totalProfit] = $this->calculateCartTotals($carts);

        return [
            'addresses' => $addresses ?? null,
            'default_address' => $default_address ?? null,
            'carts' => $carts ?? null,
            'price_send' => $price_send['total_cost'] ?? 1000,
            'totalPrice' => $amountPayable ?? null,
            'amountPayable' => $amountPayable + $price_send['total_cost'] ?? null,
            'serviceName' =>$price_send['shipping_prices'][0]['service_price'][0]['serviceName'],
            'slaDays' => $this->calculateSendDate(data_get($price_send, 'shipping_prices.0.service_price.0.slaDays')),
        ];
    }


    private function CalculatePriceSend($address, $carts)
    {
        if (!$address || !$address->city || !$address->city->postex_id) {
            \Log::warning('آدرس نامعتبر یا شهر فاقد postex_id', [
                'address_id' => $address->id ?? null
            ]);
            return ['error' => 'آدرس نامعتبر یا شهر فاقد postex_id'];
        }

        if ($carts->isEmpty()) {
            \Log::warning('سبد خرید خالی برای کاربر', ['user_id' => Auth::id()]);
            return ['error' => 'سبد خرید خالی است'];
        }

        $getTotalPack = getTotalPack($carts);

        $totalPrice = 0 ; // تومان
        foreach ($carts as $cart) {
            $price = $cart->product->price /10 ;
            $discount =$this->getDiscountService->getDiscount($cart->product->id);
            if($discount['status'] == 'success'){
                $discount_percentage =$discount['data']['percentage'] ;
                $price = $price - (floatval($discount_percentage) * $price / 100);
            }
            $count = $cart->count ;
            $totalPrice = $totalPrice + ($price * $count);
        }


        if($totalPrice <= 30000000){
            $payload = [
                'from_city_code' => 175,
                'collection_type' => 'postex_drop_off',
                'value_added_service' => (object)[],
                'courier' => (object)[
                    'courier_code' => 'IR_POST',
                    'service_type' => 'EXPRESS',
                ],
                'parcels' => [
                    (object)[
                        'custom_parcel_id' => '',
                        'to_city_code' => $address->city->postex_id,
                        'payment_type' => 'SENDER',
                        'parcel_properties' => (object)[
                            'length' => ceil($getTotalPack['length']),
                            'width' => ceil($getTotalPack['width']),
                            'height' => ceil($getTotalPack['height']),
                            'total_weight' => (int) $getTotalPack['weight'],
                            'is_fragile' => false,
                            'is_liquid' => false,
                            'total_value' => (int) $totalPrice,
                            'pre_paid_amount' => 0,
                            'total_value_currency' => 'IRR',
                            'box_type_id' => $getTotalPack['id_post']?? 13,
                        ],
                    ],
                ],
            ];
            try {
                $POSTX_API_KEY = 'postex_429ce738bc6d4e1eWHkuyp7mcPkoA5tVQLJ0QvuLwkVv3';
                $response = Http::withHeaders([
                    'x-api-key' => $POSTX_API_KEY,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->withOptions([
                    'verify' => config('services.shiliran.cert'),
                ])->post('https://api.postex.ir/api/v1/shipping/quotes', $payload);

                $resData = $response->json();
                if (isset($resData['isSuccess']) && !$resData['isSuccess']) {
                    return ['error' => $resData['message']];
                }
                if ($response->failed()) {
                    \Log::error('Postex Quote Failed', [
                        'payload' => $payload,
                        'response' => $response->json(),
                    ]);
                    return ['error' => 'اختلال در سیستم پست'];
                }

                return $response->json();
            }
            catch (\Exception $e) {
                \Log::error('Postex Quote Exception', [
                    'message' => $e->getMessage(),
                    'payload' => $payload,
                ]);
                return ['error' => 'استثنا در ارتباط با سرویس Postex: ' . $e->getMessage()];
            }
        }else{
            return ['error'=>'با عرض پورزش در حال حاضر امکان ارسال بار با ارزش بیش از 30 میلیون امکانپذیر نمی باشد'];
        }

    }

    public function calculateCartTotals($carts, $forView = true)
    {
        $totalPrice = 0;
        $amountPayable = 0;
        $totalProfit = 0;

        foreach ($carts as $item) {
            $discount = $this->getDiscountService->getDiscount($item->product_id);
            $productPrice = $item->product->price;
            $discountPercentage = $discount['data']['percentage'] ?? 0;
            $productPriceDiscount = $discountPercentage > 0
                ? $productPrice * (1 - ($discountPercentage / 100))
                : $productPrice;

            $discountAmount = $productPrice - $productPriceDiscount;
            $totalProfit += $discountAmount * $item->count; // مبلغ تخفیف کل سبد خرید
            $totalPrice += $productPrice * $item->count; // مبلغ کل سبد خرید بدون اعمال تخفیف
            $amountPayable = $totalPrice - $totalProfit; //مبلغ قابل پرداخت مشتری کسر شده تخفیف از مبلغ کل سبد

            if ($forView) {
                $item->discount_name = $discount['data']['discount_type_fa'] ?? ""; // نام تخفیف اعمال شده بر روی محصول
                $item->percentage = $discountPercentage > 0 ? $discountPercentage . "%" : ""; // مقدار تخفیف وارد شده بر روی محصول
                $item->productPrice = $productPrice; // قیمت اصلی محصول بدون محاسبه تخفیف
                $item->totalDiscount = $discountAmount * $item->count; // محاسبه مبلغ هر محصول بر اسا تعداد با تخفیف اعمال شده بر روی محصول
            }
        }

        return [$totalPrice, $amountPayable, $totalProfit];
    }

    public function calculateSendDate($text)
    {
        // استخراج اعداد از رشته
        preg_match_all('/\d+/', $text, $matches);
        if (empty($matches[0]) || count($matches[0]) < 2) {
            return 'از 3 تا 7 روز کاری';
        }
        $start = (int)$matches[0][0];
        $end   = (int)$matches[0][1];
        // گرد کردن هر کدام به بالا تا مضرب 24
        $startRounded = ceil($start / 24) * 24;
        $endRounded   = ceil($end / 24) * 24;
        // تبدیل به روز کاری
        $startDays = $startRounded / 24;
        $endDays   = $endRounded / 24;
        // افزودن یک روز به انتها
        $endDays += 1;
        return "از {$startDays} تا {$endDays} روز کاری";
    }

}
