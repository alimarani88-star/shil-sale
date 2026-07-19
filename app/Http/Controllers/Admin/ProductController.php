<?php

namespace App\Http\Controllers\Admin;

use App\CustomClass\Jdf;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\Image;
use App\Services\ImageUploadService;
use App\Services\ShiliranApiInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\Product_attributes;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    protected ShiliranApiInterface $api;

    public function __construct(ShiliranApiInterface $api)
    {
        $this->api = $api;
    }

    public function A_show_product()
    {

        $products = Product::with('images')->orderBy('created_at', 'desc')->get();


        return view('Admin.Product.A_show_product', compact('products'));
    }

    public function A_create_product()
    {
        $groups = $this->api->getGroups();

        if (empty($groups)) {
            return redirect()->route('A_show_product')
                ->with('swal-error', 'عدم ارتباط با سرور شیل اپ');
        }


        $attributes = Attribute::all();

        return view('Admin.Product.A_create_product', compact('attributes', 'groups'));
    }

    public function ajax_A_create_product(Request $request)
    {
        $groupId = $request->input('group_id');
        $articles = $this->api->getItemsByGroupId((int)$groupId);


        if (isset($articles) && !empty($articles) && count($articles) > 0) {
            return response()->json([
                'data' => $articles,
                'message' => 'اطلاعات با موفقیت دریافت شد',
                'status' => 'success'
            ]);
        } else {
            return response()->json([
                'data' => '',
                'message' => 'دریافت اطلاعات ناموفق بود',
                'status' => 'error'
            ]);
        }
    }

    public function A_s_create_product111111(ProductRequest $request)
    {
        $inputs = $request->safe()->all();


        $check = Product::where('product_id_in_app', $inputs['product_name_id_in_app'])->first();
        if ($check) {
            return redirect()->route('A_show_product')
                ->with('toast-success', "این محصول قبلا ثبت شده است");
        }


        $shGroupId=$inputs['product_group_id_in_app'];

        $mainGroupId=$this->api->getGroupById((int)$shGroupId);

        $date = new Jdf();

        $data = [];
        $data['product_name'] = $inputs['product_name'];
        $data['group_id_in_app'] = $inputs['product_group_id_in_app'];
        $data['product_id_in_app'] = $inputs['product_name_id_in_app'];
        $data['main_group_id_in_app'] = $mainGroupId['data']['main_group_id'];
        $data['price'] = $inputs['price'];
        $data['price_unit'] = $inputs['price_unit'];
        $data['status'] = $inputs['status'];
        $data['marketable'] = $inputs['marketable'];
        $data['description'] = $inputs['description'];
        $data['slug'] = $this->generateUniqueProductSlug($inputs['product_name']);

        $data['sales_start_date'] = $date->toMiladi($request->sales_start_date);
        $data['sales_end_date'] = $date->toMiladi($request->sales_end_date);

        $data['user_id'] = Auth::id();
        $data['user_name'] = Auth::user()->name;


        $month = now()->format('Y-m');
        $path = "product/{$month}";


        DB::beginTransaction();
        try {

            $product = Product::create($data);
            foreach (['image1', 'image2', 'image3', 'image4', 'image5'] as $index => $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $disk=Storage::disk('public');
                    if (!($disk->exists($path))) {
                        $disk->makeDirectory($path);
                    }
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                    $disk->putFileAs($path, $file, $filename);

                    Image::create([
                        'image_url' => $path.'/'.$filename,
                        'imageable_id' => $product->id,
                        'imageable_type' => Product::class,
                        'primary' => $field === 'image5' ? 2 : 1,
                        'position' => $index + 1,
                    ]);
                }
            }


            if ($request->filled('meta_key') && $request->filled('meta_value')) {
                $metas = array_combine($request->meta_key, $request->meta_value);
                foreach ($metas as $key => $value) {
                    $at=Attribute::find($key);
                    Product_attributes::create([
                        'product_id' => $product->id,
                        'meta_key' => $key,
                        'meta_value' => $value,
                        'meta_name' => $at->attribute,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('A_show_product')
                ->with('toast-success', 'محصول جدید با موفقیت ثبت شد.');

        } catch (\Exception $e) {
            DB::rollBack();
            //dd($e->getMessage());
            return back()->withErrors(['error' => 'خطا در ثبت محصول: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function A_s_create_product(ProductRequest $request)
    {
        $inputs = $request->safe()->all();


        $check = Product::where('product_id_in_app', $inputs['product_name_id_in_app'])->first();
        if ($check) {
            return redirect()->route('A_show_product')
                ->with('toast-success', "این محصول قبلا ثبت شده است");
        }


        $shGroupId=$inputs['product_group_id_in_app'];

        $mainGroupId=$this->api->getGroupById((int)$shGroupId);

        $date = new Jdf();

        $data = [];
        $data['product_name'] = $inputs['product_name'];
        $data['group_id_in_app'] = $inputs['product_group_id_in_app'];
        $data['product_id_in_app'] = $inputs['product_name_id_in_app'];
        $data['main_group_id_in_app'] = $mainGroupId['data']['main_group_id'];
        $data['price'] = $inputs['price'];
        $data['price_unit'] = $inputs['price_unit'];
        $data['status'] = $inputs['status'];
        $data['marketable'] = $inputs['marketable'];
        $data['description'] = $inputs['description'];
        $data['slug'] = $this->generateUniqueProductSlug($inputs['product_name']);

        $data['sales_start_date'] = $date->toMiladi($request->sales_start_date);
        $data['sales_end_date'] = $date->toMiladi($request->sales_end_date);

        $data['user_id'] = Auth::id();
        $data['user_name'] = Auth::user()->name;


        $month = now()->format('Y-m');
        $path = "product/{$month}";


        DB::beginTransaction();
        try {

            $product = Product::create($data);
            foreach (['image1', 'image2', 'image3', 'image4', 'image5'] as $index => $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $disk=Storage::disk('public');
                    if (!($disk->exists($path))) {
                        $disk->makeDirectory($path);
                    }
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                    $disk->putFileAs($path, $file, $filename);

                    Image::create([
                        'image_url' => $path.'/'.$filename,
                        'imageable_id' => $product->id,
                        'imageable_type' => Product::class,
                        'primary' => $field === 'image5' ? 2 : 1,
                        'position' => $index + 1,
                    ]);
                }
            }


            if ($request->filled('meta_key') && $request->filled('meta_value')) {
                $metas = array_combine($request->meta_key, $request->meta_value);
                foreach ($metas as $key => $value) {
                    $at=Attribute::find($key);
                    Product_attributes::create([
                        'product_id' => $product->id,
                        'meta_key' => $key,
                        'meta_value' => $value,
                        'meta_name' => $at->attribute,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('A_show_product')
                ->with('toast-success', 'محصول جدید با موفقیت ثبت شد.');

        } catch (\Exception $e) {
            DB::rollBack();
            //dd($e->getMessage());
            return back()->withErrors(['error' => 'خطا در ثبت محصول: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function A_edit_product(Product $product)
    {

        $groups = $this->api->getGroups();

        if (empty($groups)) {
            return redirect()->route('A_show_product')
                ->with('swal-error', 'عدم ارتباط با سرور شیل اپ');
        }

        $attributes = Attribute::all();
        $date = new Jdf();

        $product->sales_start_date = $product->sales_start_date ? $date->toJalali($product->sales_start_date) : '';
        $product->sales_end_date = $product->sales_end_date ? $date->toJalali($product->sales_end_date) : '';

        $product_Meta = Product_attributes::where('product_id', $product->id)->get();


        return view('Admin.Product.A_edit_product', compact('product', 'groups', 'attributes', 'product_Meta'));
    }

    public function A_s_edit_product(ProductRequest $request, Product $product)
    {
        $date = new Jdf();
        $inputs = $request->safe()->all();

        $data = [
            'product_name' => $inputs['product_name'],
            //'group_id_in_app' => $inputs['product_group_id_in_app'],
            //'product_id_in_app' => $inputs['product_name_id_in_app'],
            'price' => $inputs['price'],
            'price_unit' => $inputs['price_unit'],
            'status' => $inputs['status'],
            'marketable' => $inputs['marketable'],
            'description' => $inputs['description'],
            'sales_start_date' => $date->toMiladi($request->sales_start_date),
            'sales_end_date' => $date->toMiladi($request->sales_end_date),
        ];

        $month = now()->format('Y-m');
        $path = "product/{$month}";

        DB::beginTransaction();
        try {

            $product->update($data);

            foreach (['image1', 'image2', 'image3', 'image4', 'image5'] as $index => $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $position = $index + 1;
                    $primary = $field === 'image5' ? 2 : 1;

                    $oldImage = $product->images()->where('position', $position)->first();

                    if ($oldImage) {
                        Storage::disk('public')->delete($oldImage->image_url);
                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                        Storage::disk('public')->putFileAs($path, $file, $filename);

                        $oldImage->update([
                            'image_url' => "{$path}/{$filename}",
                            'primary' => $primary,
                            'updated_at' => now(),
                        ]);
                    } else {
                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                        Storage::disk('public')->putFileAs($path, $file, $filename);

                        Image::create([
                            'image_url' => "{$path}/{$filename}",
                            'imageable_id' => $product->id,
                            'imageable_type' => Product::class,
                            'position' => $position,
                            'primary' => $primary,
                        ]);
                    }
                }
            }


            if ($request->filled('meta_key') && $request->filled('meta_value')) {
                $metas = array_combine($request->meta_key, $request->meta_value);

                Product_attributes::where('product_id', $product->id)
                    ->whereNotIn('meta_key', array_keys($metas))
                    ->delete();

                foreach ($metas as $key => $value) {
                    if (trim($key) === '') continue;

                    $attribute = Attribute::find($key);
                    if (!$attribute) continue;

                    $meta = Product_attributes::firstOrNew([
                        'product_id' => $product->id,
                        'meta_key' => $key,
                    ]);

                    $meta->meta_value = $value;
                    $meta->meta_name = $attribute->attribute;
                    $meta->save();
                }
            } else {
                Product_attributes::where('product_id', $product->id)->delete();
            }

            DB::commit();

            return redirect()->route('A_show_product')
                ->with('toast-success', 'محصول با موفقیت ویرایش شد ✅');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا در ویرایش محصول: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function A_inactive_product(Product $product)
    {
        $product->delete();
        $product->images()->delete();
        $product->meta()->delete();

        return redirect()->route('A_show_product')->with('swal-success', 'محصول شما با موفقیت حذف شد');
    }

    public function get_image_by_id($id)
    {
        $file1 = Image::find($id);

        if ($file1) {
            return Storage::disk('public')->response($file1->image_url);
        }else{
            return 1;
        }
    }

    private function generateUniqueProductSlug(string $productName): string
    {
        $slugSource = $this->buildSlugSourceFromKeywords($productName);
        $baseSlug = Str::slug($slugSource);
        if ($baseSlug === '') {
            $baseSlug = 'product';
        }

        $slug = $baseSlug;
        $suffix = 2;

        while (
            Product::withTrashed()
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function buildSlugSourceFromKeywords(string $name): string
    {
        $normalizedName = str_replace(
            ['ك', 'ي', '‌'],
            ['ک', 'ی', ' '],
            $name
        );

        foreach ($this->getSlugReplacements() as $keyword => $replacement) {
            $normalizedName = str_replace($keyword, $replacement, $normalizedName);
        }

        $normalizedName = preg_replace('/\s+/u', ' ', $normalizedName) ?? $normalizedName;

        return trim($normalizedName);
    }

    private function getSlugReplacements(): array
    {
        static $cachedReplacements = null;
        if ($cachedReplacements !== null) {
            return $cachedReplacements;
        }
        

       $slugReplacements = [
            // برند
            'شیل ایران' => 'shiliran',
            'شیل' => 'shil',
            'ایران' => 'iran',

            // کلیدها
            'کلید مینیاتوری تک فاز' => 'single-phase-miniature-circuit-breaker',
            'کلید مینیاتوری تک پل' => 'single-pole-miniature-circuit-breaker',
            'کلید مینیاتوری سه پل' => 'three-pole-miniature-circuit-breaker',
            'کلید مینیاتوری' => 'miniature-circuit-breaker',

            'کلید اتوماتیک فیکس موتور دار' => 'fixed-motorized-mccb',
            'کلید اتوماتیک فیکس' => 'fixed-mccb',
            'کلید اتوماتیک' => 'mccb',

            'کلید' => 'switch',
            'مینیاتوری' => 'miniature',
            'اتوماتیک' => 'automatic',
            'فیکس' => 'fixed',
            'موتور دار' => 'motorized',
            'موتوری' => 'motorized',

            // فاز و پل
            'تک فاز' => 'single-phase',
            'سه فاز' => 'three-phase',
            'تک پل' => 'single-pole',
            'سه پل' => 'three-pole',
            'پل' => 'pole',
            'فاز' => 'phase',

            // مشخصات الکتریکی
            'آمپر' => 'amp',
            'کیلو وات' => 'kw',
            'ولت' => 'voltage',
            'وات' => 'watt',
            'قدرت قطع' => 'breaking-capacity',
            'قطع' => 'breaking',
            'تیپ' => 'type',

            // تجهیزات برق
            'چراغ سیگنال' => 'signal-lamp',
            'کنتاکتور' => 'contactor',
            'استابلایزر تک فاز' => 'single-phase-stabilizer',
            'استابلایزر سه فاز' => 'three-phase-stabilizer',
            'استابلایزر' => 'stabilizer',
            'اینورتر سه فاز' => 'three-phase-inverter',
            'اینورتر خورشیدی' => 'solar-inverter',
            'اینورتر' => 'inverter',

            // سروو و دیجیتال
            'دیجیتال سروو موتوری' => 'digital-servo-motorized',
            'سروو موتوری ایستاده' => 'standing-servo-motorized',
            'دیجیتال' => 'digital',
            'سروو' => 'servo',
            'ایستاده' => 'standing',

            // شیرینگ و کابل‌بند
            'شیرینگ حرارتی ارت زرد و سبز' => 'earth-yellow-green-heat-shrink',
            'شیرینگ حرارتی مشکی' => 'black-heat-shrink',
            'شیرینگ حرارتی زرد' => 'yellow-heat-shrink',
            'شیرینگ حرارتی قرمز' => 'red-heat-shrink',
            'شیرینگ حرارتی آبی' => 'blue-heat-shrink',
            'شیرینگ حرارتی سبز' => 'green-heat-shrink',
            'شیرینگ حرارتی' => 'heat-shrink',
            'شیرینگ' => 'shrink',
            'حرارتی' => 'heat',
            'بست کمربندی' => 'cable-tie',
            'بست' => 'tie',
            'کمربندی' => 'cable-tie',

            // شستی و فرمان
            'شستی جرثقیل ضد آب تک سرعته' => 'waterproof-single-speed-crane-push-button',
            'شستی جرثقیل ضد آب' => 'waterproof-crane-push-button',
            'شستی جرثقیل' => 'crane-push-button',
            'ضد آب' => 'waterproof',
            'تک سرعته' => 'single-speed',
            'شستی' => 'push-button',
            'جرثقیل' => 'crane',
            'فرمان' => 'control',

            // کنتاکت
            'کنتاکت باز' => 'normally-open-contact',
            'کنتاکت بسته' => 'normally-closed-contact',
            'کنتاکت' => 'contact',
            'باز' => 'open',
            'بسته' => 'closed',

            // رنگ‌ها
            'ارت زرد و سبز' => 'earth-yellow-green',
            'زرد و سبز' => 'yellow-green',
            'مشکی' => 'black',
            'سفید' => 'white',
            'قرمز' => 'red',
            'زرد' => 'yellow',
            'آبی' => 'blue',
            'سبز' => 'green',

            // واحدها و اندازه‌ها
            'میلی متر' => 'mm',
            'متری' => 'meter',
            'متر' => 'meter',
            'سایز' => 'size',
            'عرض' => 'width',

            // سایر کلمات پرتکرار
            'باکالیتی' => 'bakelite',
            'دار' => 'with',
            'مدل' => 'model',
        ];
        uksort($slugReplacements, function (string $left, string $right): int {
            $leftLength = function_exists('mb_strlen') ? mb_strlen($left, 'UTF-8') : strlen($left);
            $rightLength = function_exists('mb_strlen') ? mb_strlen($right, 'UTF-8') : strlen($right);
            return $rightLength <=> $leftLength;
        });
        $cachedReplacements = $slugReplacements;
        return $cachedReplacements;
    }


}

