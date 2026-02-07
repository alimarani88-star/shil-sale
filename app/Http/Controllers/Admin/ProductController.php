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


}


