<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Category;
use App\Models\Image;
use App\Models\Post;
use App\Services\ShiliranApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function A_create_category()
    {
        $ShiliranApiService = new ShiliranApiService();
        $res = $ShiliranApiService->getGroups();
        $app_groups = collect($res['data'])->toArray();
        $res2 = $ShiliranApiService->getMainGroups();
        $app_main_groups = collect($res2['data'])->toArray();

        $categories = Category::get();
        return view('Admin.Category.A_create_category', compact('categories' , 'app_groups', 'app_main_groups'));
    }

    public function A_edit_category($id)
    {
        $category = Category::findOrfail($id);
        $image = Image::where('imageable_id', $id)
            ->where('imageable_type', 'App\Models\Category')
            ->where('primary', 1)
            ->first();
        $categoryData = [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $image ? $image->id : '',
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
            'type' => $category->type,
            'app_id' => $category->app_id,
            'app_group_type' => $category->app_group_type,
        ];

        if ($category->type == 'post') {
            $categories = Category::where('type', 'post')->select('name', 'id')->get();
        } elseif ($category->type == 'product') {
            $categories = Category::where('type', 'product')->select('name', 'id')->get();
        } else {
            $categories = Category::select('name', 'id')->get();
        }

        $ShiliranApiService = new ShiliranApiService();
        $res = $ShiliranApiService->getGroups();
        $app_groups = collect($res['data'])->toArray();
        $res2 = $ShiliranApiService->getMainGroups();
        $app_main_groups = collect($res2['data'])->toArray();

        return view('Admin.Category.A_edit_category', compact('categoryData', 'categories' , 'app_groups' , 'app_main_groups'));
    }

    public function A_categories(Request $request)
    {
        $categories = Category::orderBy('id' , 'desc')->get();
        foreach ($categories as &$category) {
            $image = Image::where('imageable_id', $category->id)->where('imageable_type', 'App\Models\Category')->first();
            $category->image = $image ? asset($image->image_url) : asset('assets/img/no-image-mini.png');
            $parent = $category->parent_id ? Category::find($category->parent_id) : null;
            $category->parent = $parent ? ($parent->parent ? '_' : '') . ($parent->name) : '-';
        }
        return view('Admin.Category.A_categories', compact('categories'));
    }

    public function A_s_create_category(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|unique:categories,name,',
            'slug' => 'required|unique:categories,slug,',
            'parent_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:product,post',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'delete_image' => 'nullable|in:0,1',
            'app_main_group' => 'nullable|numeric',
            'app_group' => 'nullable|numeric',
        ]);
        DB::beginTransaction();
        try {

            $appGroupType = null;
            $appId = null;

            if ($request->filled('app_group')) {
                $appGroupType = 'group';
                $appId = $request->input('app_group');
            } elseif ($request->filled('app_main_group')) {
                $appGroupType = 'main';
                $appId = $request->input('app_main_group');
            }

            $category = Category::create([
                'name' => $request->input('name'),
                'slug' => to_slug($request->input('slug')),
                'type' => $request->input('type'),
                'parent_id' => $request->input('parent_id', 0),
                'app_group_type' => $appGroupType,
                'app_id' => $appId,
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $month = now()->format('Y-m');
                $path = "category/{$month}";
                Storage::disk('public')->putFileAs($path, $file, $filename);
                Image::create([
                    'image_url' => "{$path}/{$filename}",
                    'imageable_id' => $category->id,
                    'imageable_type' => Category::class,
                    'primary' => 1,
                    'alt' => $request->input('name', $request->input('name')),
                ]);
            }
            DB::commit();
            return redirect()->route('A_edit_category', ['id' => $category->id])->with('toast-success', 'دسته بندی با موفقیت ایجاد شد');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('swal-error', $exception->getMessage());
        }

    }

    public function A_s_edit_category(Request $request, $id)
    {
        $category = Category::find($id);
        $validated = $request->validate([
            'name' => 'required|unique:categories,name,' . ($category ? $category->id : ''),
            'slug' => 'required|unique:categories,slug,' . ($category ? $category->id : ''),
            'parent_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:product,post',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'delete_image' => 'nullable|in:0,1',
            'app_main_group' => 'nullable|numeric',
            'app_group' => 'nullable|numeric',
        ]);
        DB::beginTransaction();
        try {
            if ($category) {
                $old_image = Image::where('imageable_id', $category->id)
                    ->where('imageable_type', Category::class)
                    ->where('primary', 1)
                    ->first();
                if ($request->delete_image == '1' && $old_image) {
                    $old_image->delete();
                    DB::commit();
                    return redirect()->back()->with('toast-success', 'تصویر با موفقیت حذف شد.');
                }
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    if ($old_image) {
                        Storage::disk('public')->putFileAs(dirname($old_image->image_url), $file, basename($old_image->image_url));
                    } else {
                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                        $month = now()->format('Y-m');
                        $path = "category/{$month}";
                        Storage::disk('public')->putFileAs($path, $file, $filename);
                        Image::create([
                            'image_url' => "{$path}/{$filename}",
                            'imageable_id' => $category->id,
                            'imageable_type' => Category::class,
                            'primary' => 1,
                            'alt' => $request->input('title', $request->input('name')),
                        ]);
                    }
                }
                $appGroupType = null;
                $appId = null;

                if ($request->filled('app_group')) {
                    $appGroupType = 'group';
                    $appId = $request->input('app_group');
                } elseif ($request->filled('app_main_group')) {
                    $appGroupType = 'main';
                    $appId = $request->input('app_main_group');
                }

                $category->update([
                    'name' => $request->input('name'),
                    'slug' => to_slug($request->input('slug')),
                    'type' => $request->input('type', 'post'),
                    'parent_id' => $request->input('parent_id', 0),
                    'app_group_type' => $appGroupType,
                    'app_id' => $appId,
                ]);

                DB::commit();
                return redirect()->back()->with('toast-success', 'دسته بندی با موفقیت به روزرسانی شد');
            } else {
                return redirect()->back()->with('swal-error', 'دسته یافت نشد');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('swal-error', $exception->getMessage());
        }

    }

    public function A_parents_categories(Request $request)
    {
        if ($request->ajax()) {

            if ($request->input('type') == 'post') {
                $categories = Category::where('type', 'post')->select('name', 'id')->get();
            } elseif ($request->input('type') == 'product') {
                $categories = Category::where('type', 'product')->select('name', 'id')->get();
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ورودی نامعتبر',
                    'data' => ''
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => '',
                'data' => $categories,
            ]);
        }
    }

    public function A_show_category_tree()
    {
        // دریافت دسته‌بندی‌های محصول
        $tree_prd = Category::getTreeProduct();
        $categoryTree1 = $this->buildTreeForJstree($tree_prd);

        // دریافت دسته‌بندی‌های پست
        $tree = Category::getTree();
        $categoryTree2 = $this->buildTreeForJstree($tree);

        // ساخت ریشه درخت با دو شاخه
        $categoryTree = [
            [
                'id' => 'root',
                'text' => 'همه دسته ها',
                'icon' => 'fa fa-sitemap',
                'a_attr' => [
                    'href' => '#',
                    'class' => 'root-node'
                ],
                'state' => [
                    'opened' => true,
                    'selected' => false
                ],
                'children' => [
                    [
                        'id' => 'product_categories',
                        'text' => 'دسته‌بندی‌های محصول',
                        'icon' => 'fa fa-box',
                        'a_attr' => [
                            'href' => '#',
                            'class' => 'category-type-node'
                        ],
                        'state' => [
                            'opened' => true,
                            'selected' => false
                        ],
                        'children' => $categoryTree1
                    ],
                    [
                        'id' => 'post_categories',
                        'text' => 'دسته‌بندی‌های پست',
                        'icon' => 'fa fa-newspaper',
                        'a_attr' => [
                            'href' => '#',
                            'class' => 'category-type-node'
                        ],
                        'state' => [
                            'opened' => true,
                            'selected' => false
                        ],
                        'children' => $categoryTree2
                    ]
                ]
            ]
        ];

        return view('Admin.Category.A_show_category_tree', ['categoryTree' => $categoryTree]);
    }
    private function buildTreeForJstree($categories, $primaryCategoryId = 0)
    {

        $tree = [];

        foreach ($categories as $category) {
            $node = [
                'id' => $category['id'],
                'text' => $category['name'],
                'icon' => '',
                'a_attr' => [
                    'href' => url('/categories/' . $category['slug']),
                    'is_primary' => $category['id'] == $primaryCategoryId,
                    'id' => $category['id'],
                ],
                'state' => [
                    'opened' => false,
                    'selected' => false
                ]
            ];

            // اضافه کردن زیرمجموعه‌ها
            $children = $category['children'];
            if (sizeof($children) > 0) {
                $node['children'] = $this->buildTreeForJstree($children, $primaryCategoryId);
            } else {
                $node['icon'] = false;
            }

            $tree[] = $node;
        }

        return $tree;
    }


}
