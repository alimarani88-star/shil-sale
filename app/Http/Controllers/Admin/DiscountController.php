<?php

namespace App\Http\Controllers\Admin;

use App\CustomClass\Jdf;
use App\Http\Controllers\Controller;

use App\Http\Requests\Admin\AmazingSaleRequest;
use App\Http\Requests\Admin\CommonDiscountRequest;
use App\Http\Requests\Admin\DiscountRequest;
use App\Http\Requests\Admin\DiscountRequests;
use App\Models\Discount;
use App\Models\DiscountProduct;
use App\Models\Product;
use App\Services\ShiliranApiInterface;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DiscountController extends Controller
{

    protected ShiliranApiInterface $api;

    public function __construct(ShiliranApiInterface $api)
    {
        $this->api = $api;
    }

    public function A_show_discount()
    {

        $discounts = Discount::orderBy('created_at', 'desc')->get();

        $date = new Jdf();

        foreach ($discounts as $discount) {
            $discount->start_date = $date->toJalali($discount->start_date);
            $discount->end_date = $date->toJalali($discount->end_date);
        }
        return view('Admin.Discount.A_show_discount', compact('discounts'));
    }

    public function A_create_discount()
    {
        return view('Admin.Discount.A_create_discount');
    }

    public function A_s_create_discount(DiscountRequest $request)
    {
        $jdf = new Jdf();
        $inputs = $request->validated();

        $inputs['start_date'] = $jdf->toMiladi($request->start_date);
        $inputs['end_date'] = $jdf->toMiladi($request->end_date);


        $exists = Discount::where('discount_name', $inputs['discount_name'])
            ->where('discount_type', $inputs['discount_type'])
            ->exists();

        if ($exists) {
            return redirect()->route('A_show_discount')
                ->with('toast-error', 'این تخفیف قبلاً اضافه شده است.');
        }

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $data = [
                'discount_name' => $inputs['discount_name'],
                'discount_type' => $inputs['discount_type'],
                'start_date' => $inputs['start_date'],
                'end_date' => $inputs['end_date'],
                'status' => $inputs['status'],
                'description' => $inputs['description'] ?? null,
                'user_id' => $user->id,
                'user_name' => $user->username,
            ];

            Discount::create($data);
            DB::commit();

            return redirect()->route('A_show_discount')
                ->with('toast-success', 'تخفیف جدید با موفقیت ثبت شد.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا در ثبت تخفیف: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function A_edit_discount(Discount $discount)
    {
        $date = new Jdf();

        $discountdata = [
            'id' => $discount->id,
            'discount_name' => $discount->discount_name,
            'discount_type' => $discount->discount_type,
            'start_date' => $date->toJalali($discount->start_date),
            'end_date' => $date->toJalali($discount->end_date),
            'status' => $discount->status,
            'description' => $discount->description,
        ];


        return view('Admin.Discount.A_edit_discount', compact('discountdata'));

    }

    public function A_s_edit_discount(DiscountRequest $request, Discount $discount)
    {
        $date = new Jdf();
        $inputs = $request->validated();

        $inputs['start_date'] = $date->toMiladi($inputs['start_date']);
        $inputs['end_date'] = $date->toMiladi($inputs['end_date']);

        $exists = Discount::where('discount_name', $inputs['discount_name'])
            ->where('discount_type', $inputs['discount_type'])
            ->where('id', '!=', $discount->id)
            ->exists();

        if ($exists) {
            return redirect()->route('A_show_discount')
                ->with('toast-error', 'این تخفیف قبلاً اضافه شده است.');
        }

        DB::beginTransaction();
        try {
            $data = [
                'discount_name' => $inputs['discount_name'],
                'discount_type' => $inputs['discount_type'],
                'start_date' => $inputs['start_date'],
                'end_date' => $inputs['end_date'],
                'status' => $inputs['status'],
                'description' => $inputs['description'] ?? null,
            ];

            $discount->update($data);
            DB::commit();

            return redirect()->route('A_show_discount')
                ->with('toast-success', 'تخفیف با موفقیت ویرایش شد .');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا در ویرایش تخفیف: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function A_inactive_discount(Discount $discount)
    {
        DiscountProduct::where('discount_id', $discount->id)->delete();

        $discount->delete();

        return redirect()->route('A_show_discount')->with('swal-success', 'تخفیف شما با موفقیت حذف شد');
    }


    public function A_show_amazingsale()
    {
        $discounts = Discount::where('discount_type', 'amazingsale')
            ->whereHas('products')
            ->with('products')
            ->orderBy('created_at', 'desc')
            ->get();

        $date = new Jdf();

        foreach ($discounts as $discount) {
            $discount->start_date = $date->toJalali($discount->start_date);
            $discount->end_date = $date->toJalali($discount->end_date);
        }

        return view('Admin.Discount.A_show_amazingsale', compact('discounts'));
    }

    public function A_create_amazingsale()
    {
        $products = Product::all();
        $discounts = Discount::where('status', 1)->Where('discount_type', 'amazingsale')->get();
        return view('Admin.Discount.A_create_amazingsale', compact('products', 'discounts'));
    }

    public function A_s_create_amazingsale(AmazingSaleRequest $request)
    {

        $inputs = $request->validated();

        $product = Product::find($inputs['product_id']);
        if (!$product) {
            return redirect()->route('A_show_amazingsale')->with('toast-error', "محصول انتخاب ‌شده یافت نشد.");
        }

        $exists = DiscountProduct::where('discount_id', $inputs['discount_id'])
            ->where('target_id', $inputs['product_id'])
            ->where('target_type', 'product')
            ->exists();

        if ($exists) {
            return redirect()->route('A_show_amazingsale')->with('toast-error', "این محصول قبلاً به این تخفیف اضافه شده است.");
        }
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $data = [
                'discount_id' => $inputs['discount_id'],
                'target_type' => 'product',
                'target_id' => $inputs['product_id'],
                'target_name' => $product->product_name,
                'percentage' => $inputs['percentage_discount'],
                'description' => $inputs['description'] ?? null,
                'user_id' => $user->id,
                'user_name' => $user->username,
            ];

            DiscountProduct::create($data);
            DB::commit();

            return redirect()->route('A_show_amazingsale')
                ->with('toast-success', 'تخفیف  شگفت انگیز جدید با موفقیت ثبت شد.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا در ثبت تخفیف: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function A_edit_amazingsale($id)
    {
        $products = Product::all();
        $discounts = Discount::where('status', 1)->Where('discount_type', 'amazingsale')->get();

        $productinfo = DiscountProduct::findOrFail($id);

        return view('Admin.Discount.A_edit_amazingsale', compact('products', 'discounts', 'productinfo'));
    }

    public function A_s_edit_amazingsale(AmazingSaleRequest $request, $id)
    {

        $inputs = $request->validated();

        $product = Product::find($inputs['product_id']);
        if (!$product) {
            return redirect()->route('A_show_amazingsale')->with('toast-error', "محصول انتخاب ‌شده یافت نشد.");
        }

        $exists = DiscountProduct::where('discount_id', $inputs['discount_id'])
            ->where('target_id', $inputs['product_id'])
            ->where('target_type', 'product')
            ->where('id', '!=', $id)
            ->whereNull('deleted_at')
            ->exists();


        if ($exists) {
            return redirect()->route('A_show_amazingsale')->with('toast-error', "این محصول قبلاً به این تخفیف اضافه شده است.");
        }

        DB::beginTransaction();
        try {
            $data = [
                'discount_id' => $inputs['discount_id'],
                'target_type' => 'product',
                'target_id' => $inputs['product_id'],
                'target_name' => $product->product_name,
                'percentage' => $inputs['percentage_discount'],
                'description' => $inputs['description'] ?? null,
            ];

            $pivot = DiscountProduct::findOrFail($id);
            $pivot->update($data);
            DB::commit();

            return redirect()->route('A_show_amazingsale')
                ->with('toast-success', 'تخفیف شگفت انگیز با موفقیت ویرایش شد.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا در ثبت تخفیف: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function A_inactive_amazingsale($id)
    {
        $pivot = DiscountProduct::findOrFail($id);
        $pivot->delete();

        return redirect()->route('A_show_amazingsale')
            ->with('swal-success', 'محصول از تخفیف شگفت انگیز به حالت غیرفعال رفت');
    }


    public function A_show_common_discount()
    {

        $discounts = DiscountProduct::join('discounts', 'discount_product.discount_id', '=', 'discounts.id')
            ->where('discounts.discount_type', 'common')
            ->whereIn('discount_product.target_type', ['product', 'group', 'main_group'])
            ->whereNull('discount_product.deleted_at')
            ->orderBy('discount_product.created_at', 'desc')
            ->get([
                'discount_product.id as dp_id',
                'discount_product.*',
                'discounts.id as discount_id',
                'discounts.discount_name',
                'discounts.discount_type',
                'discounts.start_date',
                'discounts.end_date'
            ]);


        $date = new Jdf();
        foreach ($discounts as $discount) {
            if ($discount->start_date) {
                $discount->start_date = $date->toJalali($discount->start_date);
            }
            if ($discount->end_date) {
                $discount->end_date = $date->toJalali($discount->end_date);
            }
        }


        return view('Admin.Discount.A_show_common_discount', compact('discounts'));
    }

    public function A_create_common_discount()
    {
        $groups = $this->api->getGroups();
        $maingroups = $this->api->getMainGroups();


        if (empty($groups) || empty($maingroups)) {
            return redirect()->route('A_show_common_discount')
                ->with('swal-error', 'عدم ارتباط با سرور شیل اپ');
        }

        $products = Product::all();
        $discounts = Discount::where('status', 1)->Where('discount_type', 'common')->get();
        return view('Admin.Discount.A_create_common_discount', compact('groups', 'maingroups', 'products', 'discounts'));
    }

    public function A_s_create_common_discount(CommonDiscountRequest $request)
    {
        $inputs = $request->validated();

        $targetType = null;
        $targetId = null;
        $targetName = null;

        if (!empty($inputs['product_id'])) {
            $targetType = 'product';
            $targetId = $inputs['product_id'];
            $product = Product::find($inputs['product_id']);
            $targetName = $product->product_name ?? null;
        } elseif (!empty($inputs['group_id'])) {
            $targetType = 'group';
            $targetId = $inputs['group_id'];
            $group = $this->api->getGroupById($inputs['group_id']);
            $targetName = $group['data']['title'] ?? null;
        } elseif (!empty($inputs['main_group_id'])) {
            $targetType = 'main_group';
            $targetId = $inputs['main_group_id'];
            $mainGroup = $this->api->getMainGroupById($inputs['main_group_id']);
            $targetName = $mainGroup['data']['name'] ?? null;
        }

        $duplicateCheck = DiscountProduct::where('discount_id', $inputs['discount_id'])
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->whereNull('deleted_at')
            ->exists();

        if ($duplicateCheck) {
            return redirect()->route('A_show_common_discount')
                ->with('toast-error', "این مورد قبلاً به این تخفیف اضافه شده است.");
        }

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $data = [
                'discount_id' => $inputs['discount_id'] ?? null,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'target_name' => $targetName,
                'percentage' => $inputs['percentage_discount'],
                'description' => $inputs['description'] ?? null,
                'user_id' => $user->id,
                'user_name' => $user->username,
            ];

            DiscountProduct::create($data);
            DB::commit();

            return redirect()->route('A_show_common_discount')
                ->with('toast-success', 'تخفیف عمومی جدید با موفقیت ثبت شد.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا در ثبت تخفیف: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function A_edit_common_discount($id)
    {
        $groups = $this->api->getGroups();
        $maingroups = $this->api->getMainGroups();

        if (empty($groups) || empty($maingroups)) {
            return redirect()->route('showCommonDiscount')
                ->with('swal-error', 'عدم ارتباط با سرور شیل اپ');
        }

        $products = Product::all();
        $discounts = Discount::where('status', 1)
            ->where('discount_type', 'common')
            ->get();

        $productinfo = DiscountProduct::findOrFail($id);

        if ($productinfo->target_type === 'group') {
            $productinfo['discount_type'] = 'group';
        } elseif ($productinfo->target_type === 'main_group') {
            $productinfo['discount_type'] = 'maingroup';
        } else {
            $productinfo['discount_type'] = 'product';
        }


        return view('Admin.Discount.A_edit_common_discount', compact(
            'groups',
            'maingroups',
            'products',
            'discounts',
            'productinfo'
        ));
    }

    public function A_s_edit_common_discount(CommonDiscountRequest $request, $id)
    {
        $inputs = $request->validated();

        $targetType = null;
        $targetId = null;
        $targetName = null;

        if (!empty($inputs['product_id'])) {
            $targetType = 'product';
            $targetId = $inputs['product_id'];
            $product = Product::find($inputs['product_id']);
            $targetName = $product->product_name ?? null;
        } elseif (!empty($inputs['group_id'])) {
            $targetType = 'group';
            $targetId = $inputs['group_id'];
            $group = $this->api->getGroupById($inputs['group_id']);
            $targetName = $group['data']['title'] ?? null;
        } elseif (!empty($inputs['main_group_id'])) {
            $targetType = 'main_group';
            $targetId = $inputs['main_group_id'];
            $mainGroup = $this->api->getMainGroupById($inputs['main_group_id']);
            $targetName = $mainGroup['data']['name'] ?? null;
        }

        $duplicateCheck = DiscountProduct::where('discount_id', $inputs['discount_id'])
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('id', '!=', $id)
            ->whereNull('deleted_at')
            ->exists();

        if ($duplicateCheck) {
            return redirect()->route('A_show_common_discount')
                ->with('toast-error', "این مورد قبلاً در این تخفیف وجود دارد. تغییری اعمال نشد.");
        }

        DB::beginTransaction();
        try {
            $data = [
                'discount_id' => $inputs['discount_id'] ?? null,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'target_name' => $targetName,
                'percentage' => $inputs['percentage_discount'],
                'description' => $inputs['description'] ?? null,
            ];

            $pivot = DiscountProduct::findOrFail($id);
            $pivot->update($data);
            DB::commit();

            return redirect()->route('A_show_common_discount')
                ->with('toast-success', 'تخفیف عمومی با موفقیت ویرایش شد.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا در ثبت تخفیف: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function A_inactive_common_discount($id)
    {
        $pivot = DiscountProduct::findOrFail($id);
        $pivot->delete();
        return redirect()->route('A_show_common_discount')->with('swal-success', 'تخفیف شما با موفقیت حذف شد');
    }


}
