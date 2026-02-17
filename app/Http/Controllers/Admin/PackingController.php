<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Category;
use App\Models\Details_pattern;
use App\Models\Image;
use App\Models\Packing_pattern;
use App\Models\Post;
use App\Models\Product;
use App\Models\Product_carton;
use App\Models\Standard_carton;
use App\Services\ShiliranApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return view('Admin.Packing.A_product_packing_list', compact('products', 'groups', 'cartons'));
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
        $cartonsList = $cartons->pluck('name', 'id');

        $data = $packers->map(function ($pack) use ($productsList, $groupsList, $cartonsList) {
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

        return response()->json(['data' => $data, 'status' => 'success', 'message' => '']);
    }

    public function A_post_packing_list()
    {

        $company_cartons = Standard_carton::where('type', 'company')->get();
        $post_cartons = Standard_carton::where('type', 'post')->get();


        return view('Admin.Packing.A_post_packing_list', compact('post_cartons', 'company_cartons'));
    }

    public function ajax_post_packing_list(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'carton_id' => 'numeric|required',
            ]);
            try {
                $patterns = Packing_pattern::where('carton_id', $request->input('carton_id'))
                    ->get()->pluck('id')->toArray();
                $cartons = Standard_carton::get()->pluck('name', 'id');
                $patterns_map = [];
                foreach ($patterns as $id) {
                    $details_rows = Details_pattern::where('pattern_id', $id)->get();
                    foreach ($details_rows as $details_row) {
                        $patterns_map[$id][] = [
                            'pattern_id' => $details_row->pattern_id,
                            'pattern_details_id' => $details_row->id,
                            'carton_id' => $details_row->carton_id,
                            'carton_name' => $cartons[$details_row->carton_id] ?? '-',
                            'quantity' => $details_row->quantity,
                        ];
                    }
                }

                return response()->json(['data' => $patterns_map, 'status' => 'success', 'message' => '']);

            } catch (\Throwable $th) {
                return response()->json(['status' => 'error', 'message' => $th->getMessage() . ' ' . $th->getLine(), 'data' => '']);
            }
        }
    }

    public function ajax_post_packing_update(Request $request)
    {
        $request->validate([
            'pattern_id' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.pattern_details_id' => 'nullable|numeric',
            'items.*.carton_id' => 'required|numeric',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        try {
            $patternId = (int)$request->pattern_id;
            $items = $request->items;

            // همه ردیف‌های فعلی این الگو
            $existing = Details_pattern::where('pattern_id', $patternId)->get();
            $existingIds = $existing->pluck('id')->map(fn($x) => (int)$x)->toArray();

            $sentIds = collect($items)
                ->pluck('pattern_details_id')
                ->filter()
                ->map(fn($x) => (int)$x)
                ->toArray();

            // حذف: هرچی قبلاً بوده ولی الان ارسال نشده
            $toDelete = array_values(array_diff($existingIds, $sentIds));
            if (!empty($toDelete)) {
                Details_pattern::where('pattern_id', $patternId)
                    ->whereIn('id', $toDelete)
                    ->delete();
            }

            foreach ($items as $item) {
                $detailId = $item['pattern_details_id'] ?? null;

                if ($detailId) {
                    Details_pattern::where('pattern_id', $patternId)
                        ->where('id', $detailId)
                        ->update([
                            'carton_id' => (int)$item['carton_id'],
                            'quantity' => (int)$item['quantity'],
                        ]);
                } else {
                    Details_pattern::create([
                        'pattern_id' => $patternId,
                        'carton_id' => (int)$item['carton_id'],
                        'quantity' => (int)$item['quantity'],
                    ]);
                }
            }

            return response()->json(['status' => 'success', 'message' => '']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }


    public function ajax_post_packing_create_pattern(Request $request)
    {
        $request->validate([
            'carton_id' => 'required|numeric',
        ]);

        try {
            $pattern = Packing_pattern::create([
                'carton_id' => (int)$request->carton_id,
            ]);

            return response()->json([
                'status' => 'success',
                'pattern_id' => $pattern->id,
                'message' => ''
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => ''
            ]);
        }
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

    public function A_delete_packing($id)
    {
        if (is_numeric($id)) {
            Product_carton::find($id)->delete();
            return redirect()->back()->with('toast-success', 'بسته با موفقیت حذف شد');
        }
    }

    public function A_carton_list()
    {
        $cartons = Standard_carton::orderBy('type', 'asc')->get();


        $types = [
            'company' => 'شرکتی',
            'post' => 'پستی',
            'default' => 'پیش‌فرض',
        ];
        foreach ($cartons as &$carton) {
            $carton->type_fa = $types[$carton->type];
        }
        return view('Admin.Packing.A_carton_list', compact('cartons'));
    }

    public function ajax_carton_list(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'id_post' => 'nullable|numeric|min:0',
            'box_weight' => 'nullable|numeric|min:0',
            'type' => 'required|in:company,post,default',
            'id' => 'nullable|numeric',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'length' => (float)$request->length,
                'width' => (float)$request->width,
                'height' => (float)$request->height,
                'id_post' => (int)($request->id_post ?? 0),
                'box_weight' => (float)($request->box_weight ?? 0),
                'type' => $request->type,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
            ];

            if ($request->filled('id')) {
                Standard_carton::where('id', (int)$request->id)->update($data);
            } else {
                Standard_carton::create($data);
            }

            return response()->json(['status' => 'success', 'message' => '']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    public function deletePacker($id)
    {
        $pack = Product_carton::findOrFail($id);
        $pack->delete();
        return response()->json(['success' => true]);
    }


}
