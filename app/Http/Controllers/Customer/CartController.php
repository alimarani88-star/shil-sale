<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\AddProductToCartRequest;
use App\Http\Requests\Customer\UpdateCartRequest;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\GetDiscountService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class CartController extends Controller
{
    public function __construct(private readonly GetDiscountService $getDiscountService, private readonly CartService $cartService) {}

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
            "product_id" => $inputs['product_id'],
            "count" => $inputs['quantity'],
            "user_id" => Auth::id(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'محصول با موفقیت به سبد خرید اضافه شد']);
    }

    public function update_count_product_cart(UpdateCartRequest $request)
    {
        $productId = $request->product_id; // محصول
        $newCount = $request->quantity; // تعداد سفارش

        $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->with('product')
            ->first();

        $cart->update(['count' => $newCount]); // آپدیت کردن تعداد سفارشات محصول

        $discount = $this->getDiscountService->getDiscount($cart->product_id);
        $productPrice = $cart->product->price;
        $discountPercentage = $discount['data']['percentage'] ?? 0;
        $productPriceDiscount = $discountPercentage > 0
            ? $productPrice * (1 - ($discountPercentage / 100))
            : $productPrice;

        $discountAmount = $productPrice - $productPriceDiscount; // دریافت مقدار تخفیف اعمال شده بر روی محصول
        $totalDiscount = $discountAmount * $cart->count; // دریافت مقدار تخفیف کل بر روی محصول بر اساس تعداد

        $carts = Cart::where('user_id', Auth::id())->with('product')->get(); // دریافت کل سبد خرید کاربر
        [$totalPrice, $amountPayable, $totalProfit] = $this->cartService->calculateCartTotals($carts); //  محاسبه دوباره دیتاهای مبلغ کل سبد خرید
        return response()->json([
            'status' => 'success',
            'message' => 'تعداد محصول با موفقیت بروزرسانی شد',
            'data' => [
                'cart' => [
                    'product_id' => $cart->product_id,
                    'product_price' => $productPrice,
                    'totalDiscount' => number_format($totalDiscount),
                    'percentage' => $discountPercentage > 0 ? number_format($discountPercentage) . "%" : "", // مقدار تخفیف اعمال شده بر روی محصول
                ],
                'totalPrice' => number_format($totalPrice),
                'AmountPayable' => number_format($amountPayable),
                'totalProfit' => number_format($totalProfit),
            ]
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
            ]
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

            if ($discount['status'] == "success" && $discount['data']['percentage'] > 0) {
                $productPrice = $productPrice * (1 - ($discount['data']['percentage'] / 100));
            }

            $totalPrice += $productPrice * $item->count;
            $item->discount_name = $discount['data']['discount_type_fa'] ?? "";
            $item->percentage = isset($discount['data']['percentage']) ? "%" . $discount['data']['percentage'] : "";
        }

        return response()->json([
            'count' => $cartCount,
            'cart' => $cart,
            'totalPrice' => $totalPrice
        ]);
    }



    public function cart_select_address()
    {
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
        $address = Address::where('user_id', Auth::user()->id)->where('user_id', Auth::id())->first();

        $send_price = Session::get('order_price_send', 0);
        $request_invoice = Session::put('request_invoice', $request->request_invoice);

        [$totalPrice, $amountPayable, $totalProfit] = $this->cartService->calculateCartTotals($carts);
        $amountPayable = $amountPayable + $send_price;

        return view('Customer.Cart.cart_select_payment_type', compact('carts', 'amountPayable'))->with('success', ' اطلاعات با موفقیت ثبت شد. لطفا نوع پرداخت را انتخاب کنید.');


    }



    public function cart_payment(Request $request)
    {


        $carts = Cart::where('user_id', Auth::id())->with(['product.images'])->get();
        $address = Address::where('user_id', Auth::user()->id)->where('user_id', Auth::id())->first();

        $send_price = Session::get('order_price_send', 0);
        $send_type = Session::get('order_type', 0);
        $request_invoice = Session::get('request_invoice', 0);
        $send_time = Session::get('order_time', 0);

        [$totalPrice, $amountPayable, $totalProfit] = $this->cartService->calculateCartTotals($carts);
        $amountPayable = $amountPayable + $send_price;

        do {
            $code = rand(10000, 99999);
        } while (Order::where('code', $code)->exists());

        DB::beginTransaction();
        try {
            $order = Order::create([
                'code' => $code,
                'customer_id' => Auth::id(),
                'customer_name' => Auth::user()->name,
                'status' => '0',
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
                    'product_name ' => $cart->product->product_name,
                    'price' => $cart->product->price,
                    'count' => $cart->count,
                    'discount' => $discount['data']['percentage'] ?? null,
                ]);
            }

            Cart::where('user_id', Auth::id())->delete();
            DB::commit();


            // Redirect to payment gateway or process payment here

            return view('Customer.Cart.cart_payment_success');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'خطایی در ثبت سفارش رخ داده است. لطفا مجددا تلاش کنید.');
        }


    }

}

