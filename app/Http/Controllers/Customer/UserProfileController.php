<?php

namespace App\Http\Controllers\Customer;

use App\CustomClass\Jdf;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerAddressRequest;
use App\Http\Requests\Customer\CustomerInfoRequest;
use App\Http\Requests\Customer\EditProfileRequest;
use App\Http\Requests\Customer\ResetPasswordRequest;
use App\Http\Requests\RegisterCodeRequest;
use App\Jobs\SendSMS;
use App\Models\Address;
use App\Models\ExhibitionCustomer;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProvinceCity;
use App\Models\User;
use App\Models\UserProfile;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Log;


class UserProfileController extends Controller
{

    public function mobile_number_verification(Request $request)
    {
        $username = session()->get('username');
        return view('Customer.Profile.mobile_number_verification', compact('username'));
    }

    public function s_mobile_number_verification(RegisterCodeRequest $request)
    {
        $username = session()->get('username');
        $verification_code = session()->get('verification_code');
        $repetition_code = session()->get('repetition_code');
        $user = User::where('username', $username)->first();

        DB::beginTransaction();
        try {
            if ((int)$repetition_code < 4) {
                if ($verification_code === '' or $username === '') {
                    DB::rollBack();
                    return redirect()->route('register')->with('error', 'لطفا ابتدا اطلاعات خود را وارد کنید.');

                } else {
                    if ($verification_code === (int)$request->verification_code && $username === $request->username) {
                        $user->update(['verified' => 1]);
                        Auth::login($user);
                        DB::commit();
                        return redirect()->route('home')->with('success', 'ثبت نام شما با موفقیت انجام شد');
                    } else {
                        session(['repetition_code' => $repetition_code + 1]);
                        DB::rollBack();
                        return back()->with('error', 'کد تأیید وارد شده صحیح نیست.');
                    }
                }
            } else {
                DB::rollBack();
                return back()->with('error', 'تکرار بیش از حد کد نادرست ');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function resend_verification_code(Request $request)
    {
        $code4 = rand(1000, 9999);
        $mobile = session()->get('username');
        session(['verification_code' => $code4]);
        session(['repetition_code' => 0]);
        $templateId = '305741';

        $parameters = [
            [
                "name" => "CODE",
                "value" => $code4
            ]
        ];
        SendSMS::dispatch($mobile, $templateId, $parameters);

        return response()->json([
            'message' => 'کد تأیید مجدداً ارسال شد.'
        ]);
    }

    public function forgot_password_verification(Request $request)
    {
        $username = session()->get('forgot_password_mobile');
        return view('Customer.Profile.forgot_password_verification', compact('username'));
    }

    public function s_forgot_password_verification(Request $request)
    {
        $forgot_password_mobile = session()->get('forgot_password_mobile');
        $forgot_password_code = session()->get('forgot_password_code');
        $repetition_forgot_password_code = session()->get('repetition_forgot_password_code');
        $user = User::where('username', $forgot_password_mobile)->first();

        DB::beginTransaction();
        try {
            if ((int)$repetition_forgot_password_code < 4) {
                if ($forgot_password_code === null || $forgot_password_mobile === null) {
                    DB::rollBack();
                    return redirect()->route('login')->with('error', 'لطفا ابتدا اطلاعات خود را وارد کنید.');
                } else {
                    if ($forgot_password_code === (int)$request->verification_code) {
                        $password = rand(10000000, 99999999);
                        $user->update(['password' => Hash::make($password)]);
                        $templateId = '263432';
                        $parameters = [
                            [
                                "name" => "CODE",
                                "value" => $password
                            ]
                        ];
                        SendSMS::dispatch($forgot_password_mobile, $templateId, $parameters);
                        session()->forget('forgot_password_code');
                        session()->forget('forgot_password_mobile');
                        DB::commit();
                        return redirect()->route('login')->with('success', 'رمز عبور جدید برای شما ارسال شد لطفا پس از ورود، رمز عبور خود را تغییر دهید.');
                    } else {
                        session(['repetition_forgot_password_code' => $repetition_forgot_password_code + 1]);
                        DB::rollBack();
                        return back()->with('error', 'کد تأیید وارد شده صحیح نیست.');
                    }
                }
            } else {
                DB::rollBack();
                return back()->with('error', 'تکرار بیش از حد کد نادرست ');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('login')->with('error', 'خطایی رخ داده است. لطفا دوباره تلاش کنید.');
        }
    }

    public function resend_forgot_password_verification(Request $request)
    {
        $code4 = rand(1000, 9999);
        $mobile = session()->get('forgot_password_mobile');
        session(['forgot_password_code' => $code4]);
        session(['repetition_forgot_password_code' => 0]);
        $templateId = '305741';

        $parameters = [
            [
                "name" => "CODE",
                "value" => $code4
            ]
        ];
        SendSMS::dispatch($mobile, $templateId, $parameters);

        return response()->json([
            'message' => 'کد تأیید مجدداً ارسال شد.'
        ]);
    }

    public function reset_password()
    {
        return view('Customer.Profile.reset_password');
    }

    public function s_reset_password(ResetPasswordRequest $request)
    {
        $inputs = $request->validated();
        $user = Auth::user();
        if (!Hash::check($inputs['old_password'], $user->password)) {
            return back()->with(['error' => 'رمز عبور قبلی نادرست است.']);
        }

//        $rules = [];
//        if ($user->type === "user") {
//            $rules['new_password'][] = Password::min(8)->letters()->mixedCase()->numbers();
//        }
//
//        $request->validate($rules);

        $user->update(['password' => Hash::make($inputs['new_password'])]);

        return back()->with('success', '.رمز عبور شما با موفقیت تغییر کرد');
    }

    public function C_profile()
    {
        $user = Auth::user();
        $userProfileInfo = UserProfile::where('user_id', $user->id)->first();

        return view('Customer.Profile.profile', compact('user', 'userProfileInfo'));
    }

    public function edit_profile()
    {
        $user = Auth::user();
        $userProfileInfo = UserProfile::where('user_id', $user->id)->first();
        return view('Customer.Profile.edit_profile', compact('user', 'userProfileInfo'));
    }

    public function profile_orders()
    {
        $user = Auth::user();
        $userProfileInfo = UserProfile::where('user_id', $user->id)->first();
        $orders = Order::Where('customer_id', $user->id)->get();

        $date = new Jdf();
        foreach ($orders as $order) {
            $order->created_at_jalali = $date->toJalali($order->created_at);
        }
        return view('Customer.Profile.profile_orders', compact('user', 'userProfileInfo', 'orders'));
    }

    public function profile_orders_return()
    {
        $user = Auth::user();
        $orders = Order::Where('customer_id', $user->id)->get();
        return view('Customer.Profile.profile_order_detail', compact('user', 'Order'));
    }



    public function profile_personal_info()
    {
        $user = Auth::user();
        $userProfileInfo = UserProfile::where('user_id', $user->id)->first();


        return view('Customer.Profile.profile_personal_info', compact('user', 'userProfileInfo'));
    }

    public function profile_favorites()
    {
        $user = Auth::user();
        $userProfileInfo = UserProfile::where('user_id', $user->id)->first();
        $productsFavorites = $user->favoriteProducts()->with('images')->get();
        return view('Customer.Profile.profile_favorites', compact('user', 'userProfileInfo', 'productsFavorites'));
    }

    public function profile_addresses()
    {
        $user = Auth::user();
        $userProfileInfo = UserProfile::where('user_id', $user->id)->first();
        $addresses = Address::where('user_id', Auth::id())->latest()->get();
        $provinces = ProvinceCity::where('parent', 0)->get();

        return view('Customer.Profile.profile_addresses', compact('user', 'addresses', 'userProfileInfo', 'provinces'));
    }

    public function s_profile_add_address(CustomerAddressRequest $request)
    {

        $inputs = $request->validated();

        $user = Auth::user();

        $data = [
            'user_id' => $user->id,
            'province_id' => $inputs['province'],
            'city_id' => $inputs['city'],
            'address' => $inputs['address'],
            'postal_code' => $inputs['postal_code'],
            'no' => $inputs['no'],
            'unit' => $inputs['unit'] ?? null,
            'recipient_first_name' => $inputs['recipient_first_name'],
            'recipient_last_name' => $inputs['recipient_last_name'],
            'mobile' => $inputs['mobile'],
        ];

        Address::create($data);

        return response()->json([
            'status' => 'success',
            'message' => '.آدرس با موفقیت ثبت شد',
        ]);
    }

    public function profile_remove_address($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'شناسه نامعتبر است.'
            ]);
        }

        $user = Auth::user();
        $deleted = Address::where('user_id', $user->id)
            ->where('id', $id)
            ->forceDelete();

        if (!$deleted) {
            return response()->json(['error' => 'Item not found']);
        }

        return response()->json(['status' => 'success']);


    }

    public function ajax_get_address($id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $address
        ]);
    }

    public function s_profile_edit_address(CustomerAddressRequest $request, $id)
    {
        $inputs = $request->validated();

        $user = Auth::user();

        $data = [
            'province_id'          => $inputs['province'],
            'city_id'              => $inputs['city'],
            'address'              => $inputs['address'],
            'postal_code'          => $inputs['postal_code'],
            'no'                   => $inputs['no'],
            'unit'                 => $inputs['unit'] ?? null,
            'recipient_first_name' => $inputs['recipient_first_name'],
            'recipient_last_name'  => $inputs['recipient_last_name'],
            'mobile'               => $inputs['mobile'],
        ];

        Address::where('id',$id)->where('user_id',$user->id)->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => '.آدرس با موفقیت ویرایش شد',
        ]);
    }


    public function s_edit_profile(EditProfileRequest $request)
    {
        $user = Auth::user();

        $inputs = $request->safe();

        $data = [
            'first_name' => $inputs['first_name'],
            'last_name' => $inputs['last_name'],
            'national_code' => $inputs['national_code'],
            'card_number' => $inputs['card_number'],
            'email' => $inputs['email'],
            'user_id' => $user->id,
            'economic_number' => $inputs['economic_number'],
            'company_national_id' => $inputs['company_national_id'],
            'type' => 'person'
        ];

        UserProfile::updateOrCreate(['user_id' => $user->id], $data);

        return redirect()->route('profile_personal_info')->with('success', 'اطلاعات با موفقیت بروزرسانی شد.');
    }

    public function remove_from_favorites($id)
    {

        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'شناسه نامعتبر است.'
            ]);
        }

        $user = Auth::user();

        $deleted = Favorite::where('user_id', $user->id)
            ->where('favoritable_id', $id)
            ->forceDelete();

        if (!$deleted) {
            return response()->json(['error' => 'Item not found']);
        }

        return response()->json(['status' => 'success']);

    }

    public function register_customer()
    {
        $cities = ProvinceCity::where('parent', '<>', 0)->get();
        return view('Customer.Profile.register_customer', compact('cities'));
    }

    public function ajax_register_customer(Request $request)
    {
        $provinceId = $request->input('province_id');

        $cities = ProvinceCity::where('parent', $provinceId)->get();

        if ($cities->isNotEmpty()) {
            return response()->json([
                'data' => $cities,
                'message' => 'اطلاعات با موفقیت دریافت شد',
                'status' => 'success',
            ]);
        }

        return response()->json([
            'data' => [],
            'message' => 'دریافت اطلاعات ناموفق بود',
            'status' => 'error',
        ]);
    }


    public function s_register_customer(CustomerInfoRequest $request)
    {
        $inputs = $request->safe()->all();

        $city = null;
        $province = null;
        $registrant_name = 'بازدید کننده';
        $isLoggedIn = Auth::check();
        if ($isLoggedIn) {
            $user = Auth::user();
            $registrant_name = $user->name;
        }

        if (!empty($inputs['city'])) {
            $city = ProvinceCity::find($inputs['city']);
            if ($city) {
                $province = ProvinceCity::find($city->parent);
            }
        }

        $data = [
            'first_name' => $inputs['first_name'],
            'last_name' => $inputs['last_name'],
            'mobile' => $inputs['mobile'],
            'province_id' => $province?->id,
            'province_name' => $province?->title,
            'city_id' => $city?->id,
            'city_name' => $city?->title,
            'company_name' => $inputs['company_name'],
            'exhibition_name' => 'نمایشگاه آبان 1404 - تهران',
            'description' => $inputs['description'] ?? null,
            'request_agency' => ($inputs['request_agency'] ?? 0) == 1 ? 1 : 0,
            'registrant_name' => $registrant_name,
        ];

        ExhibitionCustomer::create($data);

        $mobile = $inputs['mobile'];

        $templateId = 634987;


        $parameters = [
            [
                "name" => "NAME",
                "value" => $inputs['last_name']
            ]
        ];

        SendSMS::dispatch($mobile, $templateId, $parameters);

        return redirect()
            ->route('register_customer')
            ->with('success', 'اطلاعات شما با موفقیت ثبت شد. آدرس لیست قیمت و کاتالوگ‌ها برای شما ارسال خواهد شد.');
    }


    public function customer_links()
    {
        return view('Customer.Profile.customer_links');
    }



    
    public function order_detail(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|integer|exists:orders,id',
        ]);

        $date = new Jdf();

        $order = Order::with('address.province', 'address.city')
            ->findOrFail($validated['order']);

        $order->created_at_jalali = $date->toJalali($order->created_at);

        $orderItems = OrderItem::where('order_id', $order->id)
            ->with('product.images')
            ->get();
        foreach ($orderItems as $item) {
            $item->product_price_percent = (int)$item->price * (1 - (int)$item->discount / 100);
        }

        return view('Customer.Profile.profile_order_detail', compact('order', 'orderItems'));
    }

}
