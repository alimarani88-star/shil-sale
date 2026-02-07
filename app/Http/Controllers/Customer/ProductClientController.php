<?php

namespace App\Http\Controllers\Customer;

use App\CustomClass\Jdf;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use App\Models\Post_type;
use App\Models\Product;
use App\Models\User;
use App\Services\ShiliranApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductClientController extends Controller
{

    public function products()
    {
        return view('Customer.Product.products');
    }

    public function products_category($slug)
    {

        $slug = preg_replace('/[^A-Za-z0-9\-_آ-ی]+/u', '', $slug);
        $category = Category::with('childrenLvl1.childrenLvl1')->where('type', 'product')
            ->where('slug', trim($slug))
            ->firstOrFail();

        $main_group_id_in_app = [];
        $group_id_in_app = [];

        $collectIds = function ($category) use (&$collectIds, &$main_group_id_in_app, &$group_id_in_app) {
            switch ($category->app_group_type) {
                case 'main':
                    $main_group_id_in_app[] = $category->app_id;
                    break;
                case 'group':
                    $group_id_in_app[] = $category->app_id;
                    break;
                case 'head':
                    if ($category->childrenLvl1->isNotEmpty()) {
                        foreach ($category->childrenLvl1 as $child) {
                            $collectIds($child);
                        }
                    }
                    break;
            }

        };

        $collectIds($category);
        $products = Product::where('status', 1)
            ->select('id', 'product_name', 'price', 'price_unit', 'main_group_id_in_app', 'group_id_in_app' , 'price','price_unit')
            ->where(function ($query) use ($main_group_id_in_app, $group_id_in_app) {
                if (!empty($main_group_id_in_app)) {
                    $query->whereIn('main_group_id_in_app', array_unique($main_group_id_in_app));
                }

                if (!empty($group_id_in_app)) {
                    $query->orWhereIn('group_id_in_app', array_unique($group_id_in_app));
                }
            })
            ->orderBy('id', 'desc')
            ->with(['images'])
            ->paginate(24);



        $dataCategory = [
            'name' => $category->name,
            'id' => $category->id,
        ];

        foreach ($products as &$product) {
            foreach ($product->images as $image) {
                if($image->primary == 1 && $image->position == 1) {
                    $product['mainImage'] = route('get_image_by_id' , ["id"=>$image->id]) ;
                }
            }

        }



        return view('Customer.Product.products', compact('dataCategory' , 'products'));
    }


    public function ajax_load_products(Request $request)
    {
        if (request()->ajax()) {
            $request->validate([
                'groupId' => 'integer',
                'tab' => 'required|string|in:new,most-view,most-seller,down-price,top-price,related',
                'page' => 'integer',
            ]);
            try {
            $tab = $request->input('tab', 'new');
            $group_id = $request->input('groupId', 0);
            $main_group_id_in_app = [];
            $group_id_in_app = [];

            if ($group_id > 0) {
                $category = Category::with('childrenLvl1.childrenLvl1')->findOrFail($group_id);

                $collectIds = function ($category) use (&$collectIds, &$main_group_id_in_app, &$group_id_in_app) {
                    switch ($category->app_group_type) {
                        case 'main':
                            $main_group_id_in_app[] = $category->app_id;
                            break;
                        case 'group':
                            $group_id_in_app[] = $category->app_id;
                            break;
                        case 'head':
                            if ($category->childrenLvl1->isNotEmpty()) {
                                foreach ($category->childrenLvl1 as $child) {
                                    $collectIds($child);
                                }
                            }
                            break;
                    }


                };

                $collectIds($category);
            }

            switch ($tab) {
                case 'new':
                    $products = Product::where('status', 1)
                        ->select('id', 'product_name', 'price', 'price_unit', 'main_group_id_in_app', 'group_id_in_app','price','price_unit')
                        ->where(function ($query) use ($main_group_id_in_app, $group_id_in_app) {
                            if (!empty($main_group_id_in_app)) {
                                $query->whereIn('main_group_id_in_app', array_unique($main_group_id_in_app));
                            }

                            if (!empty($group_id_in_app)) {
                                $query->orWhereIn('group_id_in_app', array_unique($group_id_in_app));
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->with(['images'])
                        ->paginate(24);



                    break;
//                case 'most-view':
//                    break;
//                case 'most-seller':
//                    break;
//                case 'down-price':
//                    break;
//                case 'top-price':
//                    break;
//                case 'related':
//                    break;
                default:
                    $products = Product::where('status', 1)
                        ->select('id', 'product_name', 'price', 'price_unit')
                        ->with(['images'])
                        ->paginate(24);
                    break;
            }


            return response()->json(['status' => 'success', 'message' => '', 'data' => $products]);
            }catch (\Exception $e){
                return response()->json(['status' => 'error' , 'message' => 'خطا در عملیات','data'=>'']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'درخواست نامعتبر', 'data' => '']);
        }
    }

}
