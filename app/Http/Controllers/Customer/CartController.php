<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\AddProductToCartRequest;
use App\Http\Requests\Customer\UpdateCartRequest;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\BmiGatewayService;
use App\Services\CartService;
use App\Services\GetDiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function __construct(
        private readonly GetDiscountService $getDiscountService,
        private readonly CartService $cartService,
        private readonly BmiGatewayService $bmiGatewayService,
    ) {}

    public function cart()
    {
        $user = Auth::user();
        $carts = Cart::where('user_id', $user->id)->with('product.images')->get();
        [$totalPrice, $amountPayable, $totalProfit] = $this->cartService->calculateCartTotals($carts);
        return view('Customer.Cart.cart', compact('carts', 'totalPrice', 'amountPayable', 'totalProfit'));
    }

    public function add_product_to_cart(AddProductToCartRequest $request)
    {
        $inputs = $request->validated();
        Cart::create([
            'product_id' => $inputs['product_id'],
            'count' => $inputs['quantity'],
            'user_id' => Auth::id(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'محصول با موفقیت به سبد خرید اضافه شد']);
    }

    public function update_count_product_cart(UpdateCartRequest $request)
    {
        $productId = $request->product_id;
        $newCount = $request->quantity;

        $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->with('product')
            ->first();

        if (!$cart) {
            return response()->json([
                'status' => 'error',
                'message' => 'محصول در سبد خرید یافت نشد',
            ], 404);
        }

        $cart->update(['count' => $newCount]);

        $discount = $this->getDiscountService->getDiscount($cart->product_id);
        $productPrice = $cart->product->price;
        $discountPercentage = $discount['data']['percentage'] ?? 0;
        $productPriceDiscount = $discountPercentage > 0
            ? $productPrice * (1 - ($discountPercentage / 100))
            : $productPrice;

        $discountAmount = $productPrice - $productPriceDiscount;
        $totalDiscount = $discountAmount * $cart->count;

        $carts = Cart::where('user_id', Auth::id())->with('product')->get();
        [$totalPrice, $amountPayable, $totalProfit] = $this->cartService->calculateCartTotals($carts);

        return response()->json([
            'status' => 'success',
            'message' => 'تعداد محصول با موفقیت بروزرسانی شد',
            'data' => [
                'cart' => [
                    'product_id' => $cart->product_id,
                    'product_price' => $productPrice,
                    'totalDiscount' => number_format($totalDiscount),
                    'percentage' => $discountPercentage > 0 ? number_format($discountPercentage) . '%' : '',
                ],
                'totalPrice' => number_format($totalPrice),
                'AmountPayable' => number_format($amountPayable),
                'totalProfit' => number_format($totalProfit),
            ],
        ]);
    }

    public function remove_from_cart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);
        $productId = $request->product_id;

        $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if (!$cart) {
            return response()->json(['status' => 'error', 'message' => 'محصول یافت نشد'], 404);
        }

        $cart->delete();

        $carts = Cart::where('user_id', Auth::id())->with('product')->get();
        [$totalPrice, $amountPayable, $totalProfit] = $this->cartService->calculateCartTotals($carts);

        return response()->json([
            'status' => 'success',
            'message' => 'محصول با موفقیت حذف شد',
            'data' => [
                'totalPrice' => $totalPrice,
                'AmountPayable' => $amountPayable,
                'totalProfit' => number_format($totalProfit),
            ],
        ]);
    }

    public function ajax_cart_header(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->with('product.images')->get();
        $cartCount = $cart->count();
        $totalPrice = 0;

        foreach ($cart as $item) {
            $discount = $this->getDiscountService->getDiscount($item->product_id);
            $productPrice = $item->product->price;

            if (($discount['status'] ?? null) === 'success' && ($discount['data']['percentage'] ?? 0) > 0) {
                $productPrice = $productPrice * (1 - ($discount['data']['percentage'] / 100));
            }

            $totalPrice += $productPrice * $item->count;
            $item->discount_name = $discount['data']['discount_type_fa'] ?? '';
            $item->percentage = isset($discount['data']['percentage']) ? '%' . $discount['data']['percentage'] : '';
        }

        return response()->json([
            'count' => $cartCount,
            'cart' => $cart,
            'totalPrice' => $totalPrice,
        ]);
    }

    public function cart_select_address()
    {
        //dd(3232);
        $addresses = Address::where('user_id', Auth::id())->get();
        if ($addresses->isEmpty()) {
            Session::put('to_url', 'cart_select_address');
            return redirect()->route('profile_addresses')
                ->with('info', 'لطفا ابتدا یک آدرس برای ارسال سفارش خود ثبت کنید.');
        }

        $data = $this->cartService->getPriceSendData();
        if (isset($data['error'])) {
            return redirect()->back()->with('error', $data['error']);
        }

        // $data['price_send']=100;
        // $data['serviceName']='test100';
        // $data['slaDays']=3;

        Session::put('order_price_send', $data['price_send']);
        Session::put('order_type', $data['serviceName']);
        Session::put('order_time', $data['slaDays']);

        

      

        return view('Customer.Cart.cart_select_address', $data);
    }

    public function ajax_change_address_default_cart(Request $request)
    {
        Address::where('user_id', Auth::id())->update(['is_default' => 0]);

        $newAddress = Address::find($request->addressId);
        if ($newAddress) {
            $newAddress->update(['is_default' => 1]);
        }

        $data = $this->cartService->getPriceSendData($newAddress);
        if (isset($data['error'])) {
            return redirect()->back()->with('error', $data['error']);
        }
        $amountPayable = $data['totalPrice'] + $data['price_send'];

        return response()->json([
            'success' => true,
            'price_send' => $data['price_send'],
            'amountPayable' => $amountPayable,
            'delivery_time' => $data['delivery_time']['slaDays'],
        ]);
    }

    public function cart_select_payment_type(Request $request)
    {
        $carts = Cart::where('user_id', Auth::id())->with(['product.images'])->get();
        $send_price = Session::get('order_price_send', 0);
        Session::put('request_invoice', $request->request_invoice);

        [, $amountPayable, ] = $this->cartService->calculateCartTotals($carts);
        $amountPayable += $send_price;

        return view('Customer.Cart.cart_select_payment_type', compact('carts', 'amountPayable'))
            ->with('success', 'اطلاعات با موفقیت ثبت شد. لطفا نوع پرداخت را انتخاب کنید.');
    }


    public function cart_payment11111(Request $request)
    {
        dd(3232);
        $order = null;
        $requestedOrderId = (int) $request->query('order', 0);

        if ($requestedOrderId > 0) {
            $order = Order::where('id', $requestedOrderId)
                ->where('customer_id', Auth::id())
                ->where('status', 0)
                ->first();

            if (!$order) {
                return redirect()->route('profile_orders')->with('error', 'سفارش قابل پرداختی پیدا نشد.');
            }
        } else {
            try {
                $order = $this->createPendingOrderFromCart();
            } catch (\Throwable $e) {
                Log::error('Error creating order for payment: ' . $e->getMessage());
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        $returnUrl = (string) config('services.bmi.callback_url', '');
        if ($returnUrl === '') {
            $returnUrl = route('payment.bmi.callback');
        }

        $requestPaymentResult = $this->bmiGatewayService->requestPayment(
            (string) $order->code,
            (int) round((float) $order->total_price),
            $returnUrl
        );


        if (!($requestPaymentResult['success'] ?? false)) {
            $errorMessage = (string) ($requestPaymentResult['message'] ?? 'ارسال درخواست پرداخت ناموفق بود.');
            return redirect()->route('profile_orders')->with('error', $errorMessage);
        }

        return redirect()->away($requestPaymentResult['redirect_url']);
    }

    public function cart_payment(Request $request)
    {
        $order = null;
        $requestedOrderId = (int) $request->query('order', 0);

        if ($requestedOrderId > 0) {
            $order = Order::where('id', $requestedOrderId)
                ->where('customer_id', Auth::id())
                ->where('status', 0)
                ->first();

            if (!$order) {
                return redirect()->route('profile_orders')->with('error', 'سفارش قابل پرداختی پیدا نشد.');
            }
        } else {
            try {
                $order = $this->createPendingOrderFromCart();
            } catch (\Throwable $e) {
                Log::error('Error creating order for payment: ' . $e->getMessage());
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        $returnUrl = (string) config('services.bmi.callback_url', '');
        if ($returnUrl === '') {
            $returnUrl = route('payment.bmi.callback');
        }

        $requestPaymentResult = $this->bmiGatewayService->requestPayment(
            (string) $order->code,
            (int) round((float) $order->total_price),
            $returnUrl
        );


        if (!($requestPaymentResult['success'] ?? false)) {
            $errorMessage = (string) ($requestPaymentResult['message'] ?? 'ارسال درخواست پرداخت ناموفق بود.');
            return redirect()->route('profile_orders')->with('error', $errorMessage);
        }

        return redirect()->away($requestPaymentResult['redirect_url']);
    }

    public function bmi_callback(Request $request)
    {
        $orderCode = (string) $request->input('OrderId', '');
        $token = (string) ($request->input('token') ?: $request->input('Token', ''));
        $resCode = (int) $request->input('ResCode', -1);

        if ($orderCode === '') {
            return redirect()->route('home')->with('error', 'شناسه سفارش از بانک دریافت نشد.');
        }

        $order = Order::where('code', $orderCode)->first();
        if (!$order) {
            return redirect()->route('home')->with('error', 'سفارش موردنظر پیدا نشد.');
        }

        if ($resCode !== 0) {
            if ((int) $order->status === 0) {
                $order->update([
                    'status_title' => 'پرداخت ناموفق - در انتظار پرداخت مجدد',
                ]);
            }

            return $this->redirectAfterPaymentCallback(
                $order,
                false,
                'پرداخت توسط بانک تایید نشد.'
            );
        }

        $verifyResult = $this->bmiGatewayService->verifyPayment($token);
        if (!($verifyResult['success'] ?? false)) {
            if ((int) $order->status === 0) {
                $order->update([
                    'status_title' => 'پرداخت ناموفق - در انتظار پرداخت مجدد',
                ]);
            }

            $message = (string) ($verifyResult['message'] ?? 'تایید نهایی پرداخت انجام نشد.');
            return $this->redirectAfterPaymentCallback($order, false, $message);
        }

        if ((int) $order->status !== 1) {
            $order->update([
                'status' => 1,
                'status_title' => 'پرداخت شده',
            ]);
        }

        $traceNo = (string) ($verifyResult['system_trace_no'] ?? '');
        $refNo = (string) ($verifyResult['retrival_ref_no'] ?? '');
        $successMessage = 'پرداخت با موفقیت انجام شد.';
        if ($traceNo !== '') {
            $successMessage .= " شماره پیگیری: {$traceNo}.";
        }
        if ($refNo !== '') {
            $successMessage .= " شماره مرجع: {$refNo}.";
        }

        return $this->redirectAfterPaymentCallback($order, true, $successMessage);
    }

    public function bmi_callback1(Request $request)
    {
        
        dd(323232323232);

    }

    public function payment_result(Request $request)
    {
        $order = null;
        $orderId = (int) $request->query('order', 0);
        $isSuccess = null;
        if ($request->has('success')) {
            $isSuccess = (int) $request->query('success') === 1;
        }
        $message = (string) session('payment_message', '');

        if ($message === '') {
            if ($isSuccess === true) {
                $message = 'پرداخت شما با موفقیت انجام شد.';
            } elseif ($isSuccess === false) {
                $message = 'پرداخت شما ناموفق بود یا توسط بانک تایید نشد.';
            } else {
                $message = 'نتیجه‌ای برای نمایش ثبت نشده است.';
            }
        }

        if ($orderId > 0 && Auth::check()) {
            $order = Order::where('id', $orderId)
                ->where('customer_id', Auth::id())
                ->first();
        }

        return view('Customer.Cart.payment_result', compact('order', 'isSuccess', 'message'));
    }

    private function createPendingOrderFromCart(): Order
    {
        $user = Auth::user();
        $carts = Cart::where('user_id', $user->id)->with(['product.images'])->get();
        if ($carts->isEmpty()) {
            throw new \RuntimeException('سبد خرید شما خالی است.');
        }

        $address = Address::where('user_id', $user->id)->where('is_default', 1)->first();
        if (!$address) {
            $address = Address::where('user_id', $user->id)->first();
        }
        if (!$address) {
            throw new \RuntimeException('ابتدا یک آدرس برای ثبت سفارش انتخاب کنید.');
        }

        $send_price = Session::get('order_price_send', 0);
        $send_type = Session::get('order_type', 0);
        $request_invoice = Session::get('request_invoice', 0);
        $send_time = Session::get('order_time', 0);

        [, $amountPayable, ] = $this->cartService->calculateCartTotals($carts);
        $amountPayable += $send_price;

        do {
            $code = rand(10000, 99999);
        } while (Order::where('code', $code)->exists());

        DB::beginTransaction();
        try {
            $order = Order::create([
                'code' => $code,
                'customer_id' => $user->id,
                'customer_name' => $user->name,
                'status' => 0,
                'status_title' => 'در انتظار پرداخت',
                'copan' => null,
                'total_price' => $amountPayable,
                'send_price' => $send_price,
                'send_type' => $send_type,
                'send_time' => $send_time,
                'address_id' => $address->id,
                'invoice' => $request_invoice,
            ]);

            foreach ($carts as $cart) {
                $discount = $this->getDiscountService->getDiscount($cart->product_id);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->product_name,
                    'price' => $cart->product->price,
                    'count' => $cart->count,
                    'discount' => $discount['data']['percentage'] ?? null,
                ]);
            }

            Cart::where('user_id', $user->id)->delete();
            DB::commit();

            return $order;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function redirectAfterPaymentCallback(Order $order, bool $isSuccess, string $message)
    {
        return redirect()
            ->route('payment_result', [
                'order' => $order->id,
                'success' => $isSuccess ? 1 : 0,
            ])
            ->with('payment_message', $message);
    }
   
}
