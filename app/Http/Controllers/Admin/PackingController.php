<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Category;
use App\Models\Image;
use App\Models\Post;
use App\Models\Product;
use App\Models\Product_carton;
use App\Models\Standard_carton;
use App\Services\ShiliranApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PackingController extends Controller
{

    public function A_product_packing_list()
    {

        $products = Product::get();
        $shilAppService = new ShiliranApiService();
        $groupsData = $shilAppService->getGroups();
        $groups = [];
        if ($groupsData['status']) {
            $groups = $groupsData['data'];
        }
        $cartons = Standard_carton::where('type', 'company')->get();
        return view('Admin.Packing.A_product_packing_list', compact( 'products', 'groups', 'cartons'));
    }
    public function ajax_product_packing_list()
    {
        $packers = Product_carton::get();
        $products = Product::get();
        $shilAppService = new ShiliranApiService();
        $groupsData = $shilAppService->getGroups();
        $groups = $groupsData['status'] ? $groupsData['data'] : [];
        $cartons = Standard_carton::where('type', 'company')->get();

        $productsList = $products->pluck('product_name', 'product_id_in_app');
        $groupsList = collect($groups)->pluck('title', 'id');
        $cartonsList =  $cartons->pluck('name', 'id');

        $data = $packers->map(function($pack) use ($productsList, $groupsList, $cartonsList) {
            return [
                'id' => $pack->id,
                'product_name' => $pack->item_id != 0 ? $productsList[$pack->item_id] : '-',
                'group_name' => $pack->group_id != 0 ? $groupsList[$pack->group_id] : '-',
                'carton_name' => $cartonsList[$pack->carton_id] ?? '-',
                'product_id' => $pack->item_id,
                'group_id' => $pack->group_id,
                'carton_id' => $pack->carton_id,
                'max_number' => $pack->max_number
            ];
        });

        return response()->json(['data' => $data , 'status' => 'success', 'message' => '']);
    }

    public function A_post_packing_list()
    {

        $cartons = Standard_carton::where('type', 'post')->get();
        return view('Admin.Packing.A_post_packing_list' , compact('cartons'));
    }

    public function ajax_fetch_packing(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'product_id' => 'numeric|required',
                'group_id' => 'numeric|required',
                'carton_id' => 'numeric|required',
            ]);
            try {
                $product_carton = null;
                if ($request->product_id > 0) {
                    $product_carton = Product_carton::where('item_id', $request->input('product_id'))
                        ->where('carton_id', $request->input('carton_id'))
                        ->first();
                } elseif ($request->group_id > 0) {
                    $product_carton = Product_carton::where('group_id', $request->input('group_id'))
                        ->where('carton_id', $request->input('carton_id'))
                        ->first();
                } else {
                    return response()->json(['status' => 'error', 'data' => '', 'message' => 'پارامترهای جست و جو ناقص']);
                }
                return response()->json(['status' => 'success', 'data' => $product_carton, 'message' => '']);
            } catch (\Exception $exception) {
                return response()->json(['status' => 'error', 'data' => '', 'message' => 'خطا در جست و جو']);
            }
        }
    }

    public function ajax_save_packing(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'product_id' => 'numeric|required',
                'group_id' => 'numeric|required',
                'carton_id' => 'numeric|required',
                'max_number' => 'numeric|required',
            ]);
            try {
                $product_carton = null;
                if ($request->product_id > 0) {
                    $product_carton = Product_carton::where('item_id', $request->input('product_id'))
                        ->where('carton_id', $request->input('carton_id'))
                        ->first();
                } elseif ($request->group_id > 0) {
                    $product_carton = Product_carton::where('group_id', $request->input('group_id'))
                        ->where('carton_id', $request->input('carton_id'))
                        ->first();
                }
                if ($product_carton) {
                    $product_carton->max_number = $request->input('max_number');
                    $product_carton->save();
                    return response()->json(['status' => 'success', 'data' => $product_carton, 'message' => 'بسته با موفقیت به روز رسانی شد']);
                } else {
                    $product_carton = Product_carton::create([
                        'item_id' => $request->input('product_id'),
                        'group_id' => $request->input('group_id'),
                        'carton_id' => $request->input('carton_id'),
                        'max_number' => $request->input('max_number'),
                    ]);
                    return response()->json(['status' => 'success', 'data' => $product_carton, 'message' => 'بسته با موفقیت ایجاد شد']);
                }

            } catch (\Exception $exception) {
                return response()->json(['status' => 'error', 'data' => '', 'message' => $exception->getMessage()]);
            }
        }
    }



    public function deletePacker($id)
    {
        $pack = Product_carton::findOrFail($id);
        $pack->delete();
        return response()->json(['success' => true]);
    }

    public function A_delete_packing($id)
    {
        if(is_numeric($id)){
            Product_carton::find($id)->delete();
            return redirect()->back()->with('toast-success', 'بسته با موفقیت حذف شد');
        }
    }
}
