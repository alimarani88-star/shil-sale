<?php

namespace App\Http\Controllers\Admin;

use App\CustomClass\Jdf;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Category;
use App\Models\Files;
use App\Models\Image;
use App\Models\Post;
use App\Models\Post_type;
use App\Models\Tag;
use App\Services\ShiliranApiService;
use DOMDocument;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{

    public function A_create_post()
    {
        $categoryTree = $this->buildTreeForJstree(Category::getTree());
        $productsTree = $this->fetchProductsCategory();
        $tags = Tag::get();
        return view('Admin.Post.A_create_post', compact('categoryTree', 'tags', 'productsTree'));
    }

    public function A_edit_post($id = null)
    {
        // دریافت پست (در صورت ویرایش)
        $post = $id ? Post::findOrFail($id) : null;

        // ساخت درخت دسته‌بندی


        $tags = Tag::get();

        $jdf = new Jdf();
        $expiredDate = $post->expired_at ? $jdf->toJalali($post->expired_at?->format('Y-m-d')) : null;
        $createdAt = $post->created_at ? $jdf->toJalali_with_time($post->created_at?->format('Y-m-d H:m')) : null;
        $updatedAt = $post->updated_at ? $jdf->toJalali_with_time($post->updated_at?->format('Y-m-d H:m')) : null;
        $publishDate = $post->published_at ? $jdf->toJalali_with_time($post->published_at?->format('Y-m-d ')) : null;

        if ($post->type == 'product') {
            $__categories = $post->categories()->whereIn('type', ['article', 'group', 'main'])->get()->map(function ($category) {
                return $category->type . '_' . $category->process_id;
            })->toArray();
        } elseif($post->type == 'category')  {
            $__categories = $post->categories()->where('type', 'category')->pluck('process_id')->toArray();
        }elseif ($post->type == 'guide') {
            $__categories = $post->categories()->whereIn('type', ['guide_article', 'guide_group', 'guide_main'])->get()->map(function ($category) {
                $cat_id = $category->type . '_' . $category->process_id;
                return str_replace('guide_', '', $cat_id);
            })->toArray();
        }
        $__tags = $post->tags->pluck('id')->toArray();
        $primary_category = $post->categories()
            ->where('type', $post->type)
            ->where('primary', 1)->first();

        $main_image = Image::where('imageable_id', $post->id)
            ->where('imageable_type', Post::class)
            ->where('primary', 1)
            ->first();
        $postData = [
            'id' => $post->id,
            'status' => $post->status,
            'title' => $post->title ?? '',
            'slug' => $post->slug ?? '',
            'summary' => $post->summary ?? '',
            'type' => $post->type ?? '',
            'content' => $post->content ?? '',
            'main_image' => $main_image->image_url ?? '',
            'main_image_id' => $main_image->id ?? '',
            'meta_keywords' => $post->meta_keywords ?? '',
            'meta_description' => $post->meta_description ?? '',
            'reading_time' => $post->reading_time ?? '',
            'allow_comments' => $post->allow_comments ? true : false,
            'prd_categories' => $post->type == 'product' || $post->type == 'guide' ? $__categories : null,
            'categories' => $post->type == 'category' ? $__categories : null,
            'tags' => $__tags,
            'publishDate' => $publishDate,
            'expiredDate' => $expiredDate,
            'author_name' => $post->author_name ?? '',
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'editor_name' => $post->editor_name ?? '',
            'alt_main_image' => $main_image->alt ?? '',
            'custom_fields' => $post->custom_fields ? unserialize($post->custom_fields)   : null,

        ];
        $categoryTree = $this->buildTreeForJstree(Category::getTree(), $primary_category?->process_id);

        $productsTree = $this->fetchProductsCategory();

        return view('Admin.Post.A_edit_post', compact('categoryTree', 'tags', 'postData', 'productsTree', 'id'));
    }

    public function A_s_create_post(PostRequest $request)
    {

        DB::beginTransaction();

        try {

            $jdf = new Jdf();
            $type = $request->input('type', 'product');

            $data = [
                'title' => $request->input('title'),
                'summary' => $request->input('summary'),
                'content' => $request->input('content'),
                'slug' => $request->filled('slug') ? to_slug($request->input('slug')) : to_slug($request->input('title')),
                'published_at' => $request->filled('publishDate') ? $jdf->toMiladi($request->input('publishDate')) : now(),
                'expired_at' => $request->filled('expiredDate') ? $jdf->toMiladi($request->input('expiredDate')) : null,
                'author_id' => Auth::id(),
                'author_name' => Auth::user()->name,
                'editor_id' => Auth::id(),
                'editor_name' => Auth::user()->name,
                'status' => $request->input('status', 'draft'),
                'meta_keywords' => $request->input('meta_keywords'),
                'meta_description' => $request->input('meta_description'),
                'allow_comments' => (int)$request->input('allow_comments', 0),
                'reading_time' => $request->input('reading_time', 0),
                'type' => $type,
            ];

            $the_post = Post::create($data);
            if ($type == 'category') {
                $categories = $request->input('categories', null); // لیست شناسه های دسته بندی
                if ($categories) {
                    $new_categories = json_decode($categories, true);

                    foreach ($new_categories as $category_id) {
                        $cat = Category::find($category_id);
                        Post_type::create([
                            'post_id' => $the_post->id,
                            'process_id' => $category_id,
                            'process_name' => $cat ? $cat->name : null,
                            'type' => 'category',
                            'primary' => $request->primary_category == $category_id ? 1 : 0,
                        ]);
                    }
                }
            }
            elseif ($type == 'product') {
                $prd_categories = $request->input('prd_categories', null);  // لیست شناسه های دسته بندی محصولات
                if ($prd_categories) {
                    $new_prd_categories = json_decode($prd_categories, true);
                    $ShiliranApiService = new ShiliranApiService();
                    $process_name = null;
                    foreach ($new_prd_categories as $prd_category) {
                        list($name, $id) = explode('_', $prd_category);
                        if ($name == 'article') {
                            $res = $ShiliranApiService->getItemById($id);
                            $data = $res['data'];
                            $process_name = $data['TITLE2'];
                        } elseif ($name == 'group') {
                            $res = $ShiliranApiService->getGroupById($id);
                            $data = $res['data'];
                            $process_name = $data['title'];
                        } elseif ($name == 'main') {
                            $res = $ShiliranApiService->getMainGroupById($id);
                            $data = $res['data'];
                            $process_name = $data['name'];
                        }
                        Post_type::create([
                            'post_id' => $the_post->id,
                            'process_id' => $id,
                            'process_name' => $process_name,
                            'type' => $name,
                            'primary' => 0
                        ]);

                    }

                }

            }
            elseif ($type == 'guide') {
                $prd_categories = $request->input('prd_categories', null);  // لیست شناسه های دسته بندی محصولات
                if ($prd_categories) {

                    $new_prd_categories = json_decode($prd_categories, true);
                    $ShiliranApiService = new ShiliranApiService();
                    $process_name = null;
                    foreach ($new_prd_categories as $prd_category) {
                        list($name, $id) = explode('_', $prd_category);
                        if ($name == 'article') {
                            $res = $ShiliranApiService->getItemById($id);
                            $data = $res['data'];
                            $process_name = $data['TITLE2'];
                        } elseif ($name == 'group') {
                            $res = $ShiliranApiService->getGroupById($id);
                            $data = $res['data'];
                            $process_name = $data['title'];
                        } elseif ($name == 'main') {
                            $res = $ShiliranApiService->getMainGroupById($id);
                            $data = $res['data'];
                            $process_name = $data['name'];
                        }
                        Post_type::create([
                            'post_id' => $the_post->id,
                            'process_id' => $id,
                            'process_name' => $process_name,
                            'type' => 'guide_'.$name,
                            'primary' => 0
                        ]);

                    }

                }

            }
            $tags = $request->input('tags', null);
            if ($tags) {
                foreach ($tags as $tag_id) {
                    $tag = Tag::find($tag_id);
                    Post_type::create([
                        'post_id' => $the_post->id,
                        'process_id' => $tag_id,
                        'process_name' => $tag ? $tag->name : null,
                        'type' => 'tag',
                        'primary' => 0
                    ]);
                }
            }
            if ($request->hasFile('main_image')) {


                $file = $request->file('main_image');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $month = now()->format('Y-m');
                $path = "post/{$month}";
                Storage::disk('public')->putFileAs($path, $file, $filename);

                Image::create([
                    'image_url' => "{$path}/{$filename}",
                    'imageable_id' => $the_post->id,
                    'imageable_type' => Post::class,
                    'primary' => 1,
                    'alt' => $request->input('alt_main_image', $request->input('title')),
                ]);
            }

            $images_path_arr = $this->getImagePath($request->input('content', ''));
            foreach ($images_path_arr as $image_path) {
                $image_path = str_replace(env('APP_URL'), '', $image_path);
                $image_path = ltrim($image_path, '/');

                $img = Image::where('imageable_id', null)
                    ->where('imageable_type', Post::class)
                    ->where('primary', 99)
                    ->latest('id')
                    ->where('image_url', 'like', "%{$image_path}%")
                    ->first();
                if ($img) {
                    $img->update([
                        'primary' => 2,
                        'imageable_id' => $the_post->id,
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('A_edit_post', ['id' => $the_post->id])->with('toast-success', 'مقاله با موفقیت ایجاد شد.');

        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('swal-error', $exception->getMessage() . ' ' . $exception->getLine());
        }
    }

    public function A_s_edit_post(PostRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        DB::beginTransaction();
        if ($request->status === 'trash' && $post != null) { // در صورت حذف پست
            try {
                $post->update([
                    'status' => 'trash'
                ]);
                DB::commit();
                return redirect()->route('A_posts')->with('toast-success', 'پست با موفقیت به زباله دان منتقل شد.');
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->with('swal-error', $exception->getMessage());
            }
        }

        try {

            $jdf = new Jdf();
            $type = $request->input('type', null);
            $customs = [];
            $fa_names = $request->input('fa_name', []);
            $en_names = $request->input('en_name', []);
            $field_types = $request->input('field_type', []);
            $fileValues = $request->file('value', []);
            $values = $request->input('value', []);

            $fi = 0; // شمارنده فایل ها
            foreach ($fa_names as $key => $fa_name) {
                if($fa_name){
                    if ($field_types[$key] === 'file') {
                        if (isset($fileValues[$fi]) && $fileValues[$fi] instanceof \Illuminate\Http\UploadedFile) {

                            $file = $fileValues[$fi];
                            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'mp4', 'mp3'];
                            $extension = strtolower($file->getClientOriginalExtension());
                            $allowedMimeTypes = [
                                'image/jpeg', 'image/png', 'image/webp',
                                'application/pdf',
                                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'video/mp4', 'audio/mpeg'
                            ];
                            $mime = $file->getMimeType();

                            if (!in_array($extension, $allowedExtensions) || !in_array($mime, $allowedMimeTypes)) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'file' => 'نوع فایل انتخابی معتبر نیست. فقط فایل‌های تصویری، صوتی، ویدیویی یا مستند مجاز هستند.'
                                ]);

                            }else{
                                $filename = uniqid() . '.' . $extension;
                                $month = now()->format('Y-m');
                                $path = "technical/{$month}";
                                Storage::disk('public')->putFileAs($path, $file, $filename);
                                $file_row = Files::create([
                                    'file_url' => "{$path}/{$filename}",
                                    'process_id' => $id,
                                    'type' => $extension,
                                ]);
                                $customs[$key] = [
                                    'fa_name' => $fa_name,
                                    'en_name' => $en_names[$key]??'',
                                    'field_type' => $field_types[$key],
                                    'value' => $file_row ? $file_row->id : '',
                                ];

                            }

                            $fi++ ;
                        }


                    } else {
                        $customs[$key] = [
                            'fa_name' => $fa_name,
                            'en_name' => $en_names[$key]??'',
                            'field_type' => $field_types[$key]??'',
                            'value' => $values[$key]??'',
                        ];
                    }

                }

            }
            $data = [
                'title' => $request->input('title'),
                'summary' => $request->input('summary'),
                'content' => $request->input('content'),
                'slug' => $request->filled('slug') ? to_slug($request->input('slug')) : to_slug($request->input('title')),
                'published_at' => $request->filled('publishDate') ? $jdf->toMiladi($request->input('publishDate')) : now(),
                'expired_at' => $request->filled('expiredDate') ? $jdf->toMiladi($request->input('expiredDate')) : null,
                'author_id' => $post ? $post->author_id : Auth::id(),
                'author_name' => $post ? $post->author_name : Auth::user()->name,
                'editor_id' => Auth::id(),
                'editor_name' => Auth::user()->name,
                'status' => $request->input('status', 'draft'),
                'meta_keywords' => $request->input('meta_keywords'),
                'meta_description' => $request->input('meta_description'),
                'allow_comments' => (int)$request->input('allow_comments', 0),
                'reading_time' => $request->input('reading_time', 0),
                'type' => $type,
                'custom_fields' => serialize(array_values($customs)),
            ];
            if ($post) {

                if ($request->hasFile('main_image')) {

                    $old_image = Image::where('imageable_id', $post->id)
                        ->where('imageable_type', Post::class)
                        ->where('primary', 1)
                        ->first();
                    $file = $request->file('main_image');
                    if ($old_image) {
                        Storage::disk('public')->putFileAs(dirname($old_image->image_url), $file, basename($old_image->image_url));
                    } else {
                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                        $month = now()->format('Y-m');
                        $path = "post/{$month}";
                        Storage::disk('public')->putFileAs($path, $file, $filename);
                        Image::create([
                            'image_url' => "{$path}/{$filename}",
                            'imageable_id' => $post->id,
                            'imageable_type' => Post::class,
                            'primary' => 1,
                            'alt' => $request->input('alt_main_image', ''),
                        ]);
                    }
                }
                $post->update($data);
                if ($type == 'category') {
                    $categories = $request->input('categories', null); // لیست شناسه های دسته بندی
                    if ($categories) {
                        $old_type_category = Post_type::where('post_id', $post->id)
                            ->where('type', 'category')
                            ->pluck('process_id')->toArray();
                        $new_categories = json_decode($categories, true);
                        // دسته‌بندی‌هایی که باید حذف شوند (موجود در قدیمی ولی وجود ندارند در جدید)
                        $categories_to_delete = array_diff($old_type_category, $new_categories);
                        if (!empty($categories_to_delete)) {
                            Post_type::where('post_id', $post->id)
                                ->where('type', 'category')
                                ->whereIn('process_id', $categories_to_delete)
                                ->delete();
                        }

                        //                        گر primary_category مخالف صفر باشد به معنای تغیر پدا کردن دسته اصلی است
                        if ($request->primary_category != 0) { // تغیر به غیر اصلی همه دسته های این پست
                            Post_type::where('post_id', $post->id)
                                ->where('type', 'category')
                                ->where('primary', 1)
                                ->update(['primary' => 0]);
                        }


                        // دسته‌بندی‌هایی که باید اضافه شوند (موجود در جدید ولی وجود ندارند در قدیمی)
                        $categories_to_add = array_diff($new_categories, $old_type_category);
                        foreach ($categories_to_add as $category_id) {
                            $cat = Category::find($category_id);
                            Post_type::create([
                                'post_id' => $post->id,
                                'process_id' => $category_id,
                                'process_name' => $cat ? $cat->name : null,
                                'type' => 'category',
                                'primary' => $request->primary_category == $category_id ? 1 : 0,
                            ]);
                        }
// دسته بندی هایی که تغیری نکرده اند
                        $categories_not_change = array_intersect($new_categories, $old_type_category);
                        if ($request->primary_category != 0) {
                            foreach ($categories_not_change as $category_id) { // صرفا برای تغیر دسته اصلی
                                if ($request->primary_category == $category_id) {
                                    $post_type = Post_type::where('post_id', $post->id)
                                        ->where('type', 'category')
                                        ->where('process_id', $category_id)
                                        ->first();
                                    $post_type->update([
                                        'primary' => 1,
                                    ]);
                                }
                            }
                        }


                    } else {
                        Post_type::where('post_id', $post->id)
                            ->where('type', 'category')
                            ->delete();
                    }
                    Post_type::where('post_id', $post->id)
                        ->whereIn('type', ['article','group','main','guide_article','guide_group','guide_main'])
                        ->delete();
                }
                elseif ($type == 'product') {
                    $prd_categories = $request->input('prd_categories', null);  // لیست شناسه های دسته بندی محصولات
                    if ($prd_categories) {

                        // استخراج دسته‌بندی‌های قدیمی از جدول به صورت آرایه
                        $old_prd_cat = Post_type::where('post_id', $post->id)
                            ->whereIn('type', ['article', 'group', 'main'])
                            ->get()->pluck('type', 'process_id')->toArray();
                        $new_prd_cat = json_decode($prd_categories, true);
                        $ShiliranApiService = new ShiliranApiService();


                        if ($old_prd_cat) {
                            // مرحله 1: شناسایی آیتم‌های جدید
                            foreach ($new_prd_cat as $prd_cat) {
                                list($prd_type, $prd_id) = explode('_', $prd_cat);

                                // اگر آیتم جدید در قدیم وجود نداشت، آن را اضافه کن
                                if (!isset($old_prd_cat[$prd_id]) || $old_prd_cat[$prd_id] != $prd_type) {
                                    $msg = 'تغیرات ذخیره نشد . %s با شناسه %s یافت نشد.';
                                    $process_name = null;
                                    if ($prd_type == 'article') {
                                        $res = $ShiliranApiService->getItemById($prd_id);
                                        if ($res['status']) {
                                            $data = $res['data'];
                                            $process_name = $data['TITLE2'];
                                        } else {
                                            DB::rollBack();
                                            return redirect()->back()->with('swal-error', sprintf($msg, $prd_type, $prd_id));
                                        }

                                    } elseif ($prd_type == 'group') {
                                        $res = $ShiliranApiService->getGroupById($prd_id);
                                        if ($res['status']) {
                                            $data = $res['data'];
                                            $process_name = $data['title'];
                                        } else {
                                            DB::rollBack();
                                            return redirect()->back()->with('swal-error', sprintf($msg, $prd_type, $prd_id));
                                        }
                                    } elseif ($prd_type == 'main') {
                                        $res = $ShiliranApiService->getMainGroupById($prd_id);
                                        if ($res['status']) {
                                            $data = $res['data'];
                                            $process_name = $data['name'];
                                        } else {
                                            DB::rollBack();
                                            return redirect()->back()->with('swal-error', sprintf($msg, $prd_type, $prd_id));
                                        }

                                    }

                                    Post_type::create([
                                        'post_id' => $post->id,
                                        'type' => $prd_type,
                                        'process_id' => $prd_id,
                                        'process_name' => $process_name,
                                    ]);
                                }
                            }

                            // مرحله 2: شناسایی آیتم‌هایی که در هر دو موجود هستند (بدون تغییر)
                            // آیتم‌هایی که در قدیم هستند ولی در جدید نیستند را حذف کن
                            foreach ($old_prd_cat as $old_id => $old_type) {
                                $found = false;

                                foreach ($new_prd_cat as $prd_cat) {
                                    list($prd_type, $prd_id) = explode('_', $prd_cat);

                                    // اگر آیتم قدیمی در جدید پیدا شد، ادامه بده
                                    if ($prd_type == $old_type && $prd_id == $old_id) {
                                        $found = true;
                                        break;
                                    }
                                }

                                // اگر آیتم قدیمی در جدید پیدا نشد، آن را حذف کن
                                if (!$found) {
                                    Post_type::where('post_id', $post->id)
                                        ->where('type', $old_type)
                                        ->where('process_id', $old_id)
                                        ->delete();
                                }
                            }

                        } else { // اگر از قبل چیزی انتخاب نشده بود
                            $new_prd_cat = json_decode($prd_categories, true);
                            $ShiliranApiService = new ShiliranApiService();
                            foreach ($new_prd_cat as $prd_cat) {
                                list($prd_type, $prd_id) = explode('_', $prd_cat);
                                $process_name = null;
                                if ($prd_type == 'article') {
                                    $res = $ShiliranApiService->getItemById($prd_id);
                                    $data = $res['data'];
                                    $process_name = $data['TITLE2'];
                                } elseif ($prd_type == 'group') {
                                    $res = $ShiliranApiService->getGroupById($prd_id);
                                    $data = $res['data'];
                                    $process_name = $data['title'];
                                } elseif ($prd_type == 'main') {
                                    $res = $ShiliranApiService->getMainGroupById($prd_id);
                                    $data = $res['data'];
                                    $process_name = $data['name'];
                                }
                                Post_type::create([
                                    'post_id' => $post->id,
                                    'process_id' => $prd_id,
                                    'process_name' => $process_name,
                                    'type' => $prd_type,
                                ]);
                            }

                        }


                    } else {
                        Post_type::where('post_id', $post->id)
                            ->whereIn('type', ['article', 'group', 'main'])
                            ->delete();
                    }

                    Post_type::where('post_id', $post->id)
                        ->where('type', ['category' , 'guide_product'])
                        ->delete();
                }
                elseif ($type == 'guide') {
                    $prd_categories = $request->input('prd_categories', null);  // لیست شناسه های دسته بندی محصولات
                    if ($prd_categories) {

                        // استخراج دسته‌بندی‌های قدیمی از جدول به صورت آرایه
                        $old_prd_cat = Post_type::where('post_id', $post->id)
                            ->whereIn('type', ['guide_article', 'guide_group', 'guide_main'])
                            ->get()->pluck('type', 'process_id')->toArray();
                        $new_prd_cat = json_decode($prd_categories, true);
                        $ShiliranApiService = new ShiliranApiService();
//dd($old_prd_cat ,$new_prd_cat );
                        if ($old_prd_cat) {
                            // مرحله 1: شناسایی آیتم‌های جدید
                            foreach ($new_prd_cat as $prd_cat) {
                                list($prd_type, $prd_id) = explode('_', $prd_cat);

                                // اگر آیتم جدید در قدیم وجود نداشت، آن را اضافه کن
                                if (!isset($old_prd_cat[$prd_id]) || $old_prd_cat[$prd_id] != 'guide_'.$prd_type) {
                                    $msg = 'تغیرات ذخیره نشد . %s با شناسه %s یافت نشد.';
                                    $process_name = null;
                                    if ($prd_type == 'article') {
                                        $res = $ShiliranApiService->getItemById($prd_id);
                                        if ($res['status']) {
                                            $data = $res['data'];
                                            $process_name = $data['TITLE2'];
                                        } else {
                                            DB::rollBack();
                                            return redirect()->back()->with('swal-error', sprintf($msg, $prd_type, $prd_id));
                                        }

                                    } elseif ($prd_type == 'group') {
                                        $res = $ShiliranApiService->getGroupById($prd_id);
                                        if ($res['status']) {
                                            $data = $res['data'];
                                            $process_name = $data['title'];
                                        } else {
                                            DB::rollBack();
                                            return redirect()->back()->with('swal-error', sprintf($msg, $prd_type, $prd_id));
                                        }
                                    } elseif ($prd_type == 'main') {
                                        $res = $ShiliranApiService->getMainGroupById($prd_id);
                                        if ($res['status']) {
                                            $data = $res['data'];
                                            $process_name = $data['name'];
                                        } else {
                                            DB::rollBack();
                                            return redirect()->back()->with('swal-error', sprintf($msg, $prd_type, $prd_id));
                                        }

                                    }

                                    Post_type::create([
                                        'post_id' => $post->id,
                                        'type' => 'guide_'.$prd_type,
                                        'process_id' => $prd_id,
                                        'process_name' => $process_name,
                                    ]);
                                }
                            }

                            // مرحله 2: شناسایی آیتم‌هایی که در هر دو موجود هستند (بدون تغییر)
                            // آیتم‌هایی که در قدیم هستند ولی در جدید نیستند را حذف کن
                            foreach ($old_prd_cat as $old_id => $old_type) {
                                $found = false;

                                foreach ($new_prd_cat as $prd_cat) {
                                    list($prd_type, $prd_id) = explode('_', $prd_cat);

                                    // اگر آیتم قدیمی در جدید پیدا شد، ادامه بده
                                    if ('guide_'.$prd_type == $old_type && $prd_id == $old_id) {
                                        $found = true;
                                        break;
                                    }
                                }

                                // اگر آیتم قدیمی در جدید پیدا نشد، آن را حذف کن
                                if (!$found) {
                                    Post_type::where('post_id', $post->id)
                                        ->where('type', $old_type)
                                        ->where('process_id', $old_id)
                                        ->delete();
                                }
                            }

                        }
                        else { // اگر از قبل چیزی انتخاب نشده بود
                            $new_prd_cat = json_decode($prd_categories, true);
                            $ShiliranApiService = new ShiliranApiService();
                            foreach ($new_prd_cat as $prd_cat) {
                                list($prd_type, $prd_id) = explode('_', $prd_cat);
                                $process_name = null;
                                if ($prd_type == 'article') {
                                    $res = $ShiliranApiService->getItemById($prd_id);
                                    $data = $res['data'];
                                    $process_name = $data['TITLE2'];
                                } elseif ($prd_type == 'group') {
                                    $res = $ShiliranApiService->getGroupById($prd_id);
                                    $data = $res['data'];
                                    $process_name = $data['title'];
                                } elseif ($prd_type == 'main') {
                                    $res = $ShiliranApiService->getMainGroupById($prd_id);
                                    $data = $res['data'];
                                    $process_name = $data['name'];
                                }
                                Post_type::create([
                                    'post_id' => $post->id,
                                    'process_id' => $prd_id,
                                    'process_name' => $process_name,
                                    'type' => 'guide_'.$prd_type,
                                ]);
                            }

                        }


                    } else {
                        Post_type::where('post_id', $post->id)
                            ->whereIn('type', ['article', 'group', 'guide_main'])
                            ->delete();
                    }

                    Post_type::where('post_id', $post->id)
                        ->where('type', ['category' , 'guide_main','guide_group','guide_article'])
                        ->delete();
                }


                $tags = $request->input('tags', null); // لیست شناسه های تگ‌ها
                $old_type_tags = Post_type::where('post_id', $post->id)
                    ->where('type', 'tag')
                    ->pluck('process_id')->toArray();

                if ($tags) {
                    // تبدیل تگ‌های جدید به آرایه (اگر JSON هستند)
                    $new_tags = is_array($tags) ? $tags : json_decode($tags, true);

                    // تگ‌هایی که باید حذف شوند (موجود در قدیمی ولی وجود ندارند در جدید)
                    $tags_to_delete = array_diff($old_type_tags, $new_tags);
                    if (!empty($tags_to_delete)) {
                        Post_type::where('post_id', $post->id)
                            ->where('type', 'tag')
                            ->whereIn('process_id', $tags_to_delete)
                            ->delete();
                    }

                    // تگ‌هایی که باید اضافه شوند (موجود در جدید ولی وجود ندارند در قدیمی)
                    $tags_to_add = array_diff($new_tags, $old_type_tags);
                    foreach ($tags_to_add as $tag_id) {
                        if (is_numeric($tag_id)) {
                            $tag = Tag::find($tag_id);
                            Post_type::create([
                                'post_id' => $post->id,
                                'process_id' => $tag_id,
                                'process_name' => $tag ? $tag->name : null,
                                'type' => 'tag'
                            ]);
                        } else {
                            $tag = Tag::create([
                                'name' => $tag_id
                            ]);
                            Post_type::create([
                                'post_id' => $post->id,
                                'process_id' => $tag->id,
                                'process_name' => $tag ? $tag->name : null,
                                'type' => 'tag',
                                'primary' => 0
                            ]);
                        }
                    }
                } else {
                    Post_type::where('post_id', $post->id)
                        ->where('type', 'tag')
                        ->delete();
                }

                $images_path_arr = $this->getImagePath($request->input('content', ''));
                foreach ($images_path_arr as $image_path) {

                    $image_path = str_replace(env('APP_URL'), '', $image_path);
                    $image_path = ltrim($image_path, '/');
                    $img = Image::where('imageable_id', $post->id)
                        ->where('imageable_type', Post::class)
                        ->where('primary', 99)
                        ->latest('id')
                        ->where('image_url', 'like', "%{$image_path}%")
                        ->first();
                    if ($img) {
                        $img->update([
                            'primary' => 2,
                        ]);
                    }
                }

                DB::commit();
                return redirect()->route('A_edit_post', ['id' => $post->id])->with('toast-success', 'مقاله با موفقیت به روزرسانی شد.');
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('swal-error', $exception->getMessage() . $exception->getLine());
        }
    }

    public function A_ajax_remove_image(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'id' => 'required|numeric'
            ]);
            try {
                $image_id = $request->input('id');
                $image = Image::find($image_id);
                if ($image) {
                    $image->delete();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'تصویر با موفقیت حذف شد.',
                        'data' => ''
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'تصویر یافت نشد.',
                        'data' => ''
                    ]);
                }
            } catch (\Exception $exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'خطا در حذف تصویر.',
                    'data' => ''
                ]);
            }

        }


    }

    public function A_s_status_post($id_status)
    {
        try {
            list($id, $status) = explode('_', $id_status);
            $post = Post::find($id);
            $post->update([
                'status' => $status == 'trash' ? 'draft' : 'trash'
            ]);
            return redirect()->back()->with('toast-success', 'پست با موفقیت به زباله دان منتقل شد.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('swal-error', $exception->getMessage());
        }
    }

    public function A_posts()
    {
        return view('Admin.Post.A_posts');
    }


    public function A_ajax_get_posts(Request $request)
    {
        if ($request->ajax()) {

            $posts = Post::query()
                ->leftJoin('post_type as pt_category', function($join) {
                    $join->on('posts.id', '=', 'pt_category.post_id')
                        ->where('pt_category.type', '=', 'category')
                        ->where(function($query) {
                            $query->where('pt_category.primary', '=', 1)
                                ->orWhereNull('pt_category.primary');
                        });
                })
                ->leftJoin('categories', 'categories.id', '=', 'pt_category.process_id')
                ->leftJoin('post_type as pt_tag', function($join) {
                    $join->on('posts.id', '=', 'pt_tag.post_id')
                        ->where('pt_tag.type', '=', 'tag');
                })
                ->leftJoin('tags', 'tags.id', '=', 'pt_tag.process_id')

                ->select([
                    'posts.id',
                    'posts.title',
                    'posts.type',
                    'posts.status',
                    'posts.author_name',
                    'posts.created_at',
                    'posts.updated_at',
                    DB::raw('MAX(categories.name) as category_name'),
                    DB::raw('GROUP_CONCAT(DISTINCT tags.name SEPARATOR ", ") as tag_name')
                ])
                ->groupBy(
                    'posts.id',
                    'posts.title',
                    'posts.type',
                    'posts.status',
                    'posts.author_name',
                    'posts.created_at',
                    'posts.updated_at',
                );


            return DataTables::of($posts)
                ->addIndexColumn()

                // ==================== ستون تصویر ====================
                ->addColumn('main_image', function ($post) {
                    $image = Image::where('imageable_id', $post->id)
                        ->where('imageable_type', Post::class)
                        ->where('primary', 1)
                        ->first();

                    $imageUrl = $image
                        ? route('get_post_image_by_id', ["id" => $image->id])
                        : asset('assets/img/no-image.jpg');

                    return '<img src=""
                                 data-src="' . $imageUrl . '"
                                 class="img-thumbnail lozad"
                                 width="50"
                                 height="50"
                                 >';
                })

                // ==================== ستون عنوان ====================
                ->addColumn('title', function ($post) {
                    return '<a href="' . route('A_edit_post', ["id" => $post->id]) . '"
                               class="text-primary font-weight-bold"
                               title="ویرایش: ' . e($post->title) . '">
                               ' . e($post->title) . '
                            </a>';
                })

                // ==================== ستون نوع ====================
                ->addColumn('type', function ($post) {
                    if (!$post->type) {
                        return '<span class="badge badge-light text-muted">نامشخص</span>';
                    }

                    $typeColors = [
                        'post' => 'primary',
                        'page' => 'info',
                        'product' => 'success',
                        'article' => 'warning'
                    ];

                    $color = $typeColors[$post->type] ?? 'secondary';

                    return '<span class="badge badge-' . $color . '">'
                        . __('public.' . $post->type)
                        . '</span>';
                })

                // ==================== ستون دسته‌بندی ====================
                ->addColumn('category', function ($post) {
                    if ($post->type == 'product') {
                        $productCategory = Post_type::where('post_id', $post->id)
                            ->whereIn('type', ['article', 'group', 'main'])
                            ->orderByRaw("FIELD(type, 'main', 'group', 'article')")
                            ->first();

                        if ($productCategory) {
                            $typeLabels = [
                                'main' => 'اصلی',
                                'group' => 'گروه',
                                'article' => 'مقاله'
                            ];

                            $label = $typeLabels[$productCategory->type] ?? $productCategory->type;

                            return '<span class="badge badge-warning">'
                                . e($productCategory->process_name)
                                . ' <small>(' . $label . ')</small>'
                                . '</span>';
                        }
                    }

                    return $post->category_name
                        ? '<span class="badge badge-info">' . e($post->category_name) . '</span>'
                        : '<span class="badge badge-light text-muted">بدون دسته</span>';
                })

                // ==================== ستون برچسب ====================
                ->addColumn('tag', function ($post) {
                    if (!$post->tag_name) {
                        return '<span class="badge badge-light text-muted">بدون برچسب</span>';
                    }

                    $tags = explode(', ', $post->tag_name);
                    $html = '';

                    foreach ($tags as $tag) {
                        $html .= '<span class="badge badge-success mr-1">' . e($tag) . '</span>';
                    }

                    return $html;
                })

                // ==================== ستون نویسنده ====================
                ->addColumn('author', function ($post) {
                    return $post->author_name
                        ? '<span class="text-dark">' . e($post->author_name) . '</span>'
                        : '<span class="text-muted">نامشخص</span>';
                })

                // ==================== ستون وضعیت ====================
                ->addColumn('status', function ($post) {
                    $statusConfig = [
                        'published' => ['color' => 'success', 'icon' => 'fa-check', 'text' => 'منتشر شده'],
                        'draft' => ['color' => 'warning', 'icon' => 'fa-edit', 'text' => 'پیش‌نویس'],
                        'trash' => ['color' => 'danger', 'icon' => 'fa-trash', 'text' => 'زباله‌دان'],
                    ];

                    $config = $statusConfig[$post->status] ?? ['color' => 'secondary', 'icon' => 'fa-question', 'text' => 'نامشخص'];


                    return '<span class="badge badge-' . $config['color'] . '">
                                <i class="fa ' . $config['icon'] . '"></i>
                                ' . $config['text'] . '
                            </span>';
                })

                // ==================== ستون عملیات ====================
                ->addColumn('action', function ($post) {
                    $editUrl = route('A_edit_post', ["id" => $post->id]);
                    $statusUrl = route('A_s_status_post', $post->id . '_' . $post->status);

                    $trashButton = $post->status == 'trash'
                        ? '<button title="بازگردانی به پیش‌نویس" style="font-size: 20px" class="btn  text-info" type="submit">
                               <i class="fa fa-undo"></i>
                           </button>'
                        : '<button title="انتقال به زباله‌دان" style="font-size: 20px;" class="btn  text-danger" type="submit">
                               <i class="fa fa-trash"></i>
                           </button>';

                    return '
                        <div class="btn-group" role="group">
                            <a class="btn text-primary" style="font-size: 20px;"
                               href="' . $editUrl . '"
                               title="ویرایش">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <form method="POST"
                                  action="' . $statusUrl . '"
                                  class="d-inline status-form">
                                ' . csrf_field() . '
                                ' . $trashButton . '
                            </form>
                        </div>
                    ';
                })
                // ==================== ستون تاریخ ایجاد ====================
                ->addColumn('created_at', function ($post) {
                    $jdf = new Jdf();
                    $created = $this->convertToPersianDate($jdf->toJalali_with_time($post->created_at)) ;
                    $created = str_replace('ساعت', '<br>ساعت', $created);
                    return
                        '<span class="text-dark text-center" style="font-size: 0.8rem">' . $created.'</span>' ;

                })

                // ==================== فیلتر ستون‌ها ====================

                ->filterColumn('title', function($query, $keyword) {
                    $query->where('posts.title', 'like', "%{$keyword}%");
                })

                ->filterColumn('type', function($query, $keyword) {
                    $query->where('posts.type', 'like', "%{$keyword}%");
                })

                ->filterColumn('author', function($query, $keyword) {
                    $query->where('posts.author_name', 'like', "%{$keyword}%");
                })

                ->filterColumn('status', function($query, $keyword) {
                    $query->where('posts.status', 'like', "%{$keyword}%");
                })

                ->filterColumn('category', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('categories.name', 'like', "%{$keyword}%")
                            ->orWhereExists(function($exists) use ($keyword) {
                                $exists->selectRaw(1)
                                    ->from('post_type')
                                    ->whereColumn('post_type.post_id', 'posts.id')
                                    ->whereIn('post_type.type', ['article', 'group', 'main'])
                                    ->where('post_type.process_name', 'like', "%{$keyword}%");
                            });
                    });
                })

                ->filterColumn('tag', function($query, $keyword) {
                    $query->where('tags.name', 'like', "%{$keyword}%");
                })

                // ==================== مرتب‌سازی ستون‌ها ====================


                ->orderColumn('title', 'posts.title $1')
                ->orderColumn('type', 'posts.type $1')
                ->orderColumn('author', 'posts.author_name $1')
                ->orderColumn('status', 'posts.status $1')
                ->orderColumn('category', 'category_name $1')
                ->orderColumn('tag', 'tag_name $1')
                ->orderColumn('created_at', 'created_at $1')

                ->rawColumns(['main_image', 'title', 'type', 'category', 'tag', 'author', 'status', 'action','created_at'])
                ->make(true);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid request',
            'data' => ''
        ]);
    }


    public function ajax_check_slug_title(Request $request)
    {
        $request->validate([
            'field' => 'required|string',
            'value' => 'required|string',
        ]);
        $field = $request->input('field'); // 'slug' یا 'title'
        $value = $request->input('value');

        if (!in_array($field, ['slug', 'title'])) {
            return response()->json(['status' => 'error', 'message' => 'فیلد نامعتبر است', 'data' => null]);
        }

        $exists = Post::where($field, $value)->exists();

        return response()->json([
            'status' => $exists ? 'error' : 'success',
            'message' => $exists ? 'این مقدار تکراری است' : 'مقدار آزاد است',
            'data' => ''
        ]);
    }


    function convertToPersianDate($dateString)
    {
        $persianMonths = [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند'
        ];
        $parts = explode(' ', trim($dateString));
        $datePart = $parts[0];
        $timePart = $parts[1] ?? null;
        $dateSegments = explode('/', $datePart);
        if (count($dateSegments) < 3) {
            return 'تاریخ نامعتبر';
        }
        $year = (int)$dateSegments[0];
        $month = (int)$dateSegments[1];
        $day = (int)$dateSegments[2];
        if ($month < 1 || $month > 12) {
            return 'ماه نامعتبر';
        }
        $monthName = $persianMonths[$month];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $formattedDay = str_replace($englishNumbers, $persianNumbers, $day);
        $formattedYear = str_replace($englishNumbers, $persianNumbers, $year);
        $result = "$formattedDay $monthName $formattedYear";
        if ($timePart) {
            $timeSegments = explode(':', $timePart);
            if (count($timeSegments) >= 2) {
                $hour = str_pad($timeSegments[0], 2, '0', STR_PAD_LEFT);
                $minute = str_pad($timeSegments[1], 2, '0', STR_PAD_LEFT);
                $formattedTime = str_replace($englishNumbers, $persianNumbers, "$hour:$minute");
                $result .= " ساعت $formattedTime";
            }
        }

        return $result;
    }

    private function fetchProductsCategory()
    {
        $productsTree = [];
        $shiliranApiService = new ShiliranApiService();
        $main_group_res = $shiliranApiService->getMainGroups();
        $groups_res = $shiliranApiService->getGroups();
        $articles_res = $shiliranApiService->getItems();
        $main_group = $main_group_res['data'];
        $groups = $groups_res['data'];
        $articles = $articles_res['data'];

        if ($main_group && $groups && $articles) {
            $productsTree = $this->buildProductsTreeForJstree($main_group, $groups, $articles);
        }
        return $productsTree;
    }

    private function buildProductsTreeForJstree($mainGroups, $groups, $articles)
    {
        $tree = [];

        // ساختاردهی به main groups
        foreach ($mainGroups as $mainGroup) {
            $mainNode = [
                'id' => 'main_' . $mainGroup['id'],
                'text' => $mainGroup['name'],
                'icon' => '',
                'a_attr' => [
                    'href' => '#'
                ],
                'state' => [
                    'opened' => false,
                    'selected' => false
                ],
                'children' => []
            ];

            // پیدا کردن گروه‌های مربوط به این main group
            $relatedGroups = array_filter($groups, function ($group) use ($mainGroup) {
                return $group['main_group_id'] == $mainGroup['id'];
            });

            // ساختاردهی به گروه‌ها
            foreach ($relatedGroups as $group) {
                $groupNode = [
                    'id' => 'group_' . $group['id'],
                    'text' => $group['title'],
                    'icon' => '',
                    'a_attr' => [
                        'href' => '#'
                    ],
                    'state' => [
                        'opened' => false,
                        'selected' => false
                    ],
                    'children' => []
                ];

                // پیدا کردن مقالات مربوط به این گروه
                $relatedArticles = array_filter($articles, function ($article) use ($group) {
                    return $article['GROUPID'] == $group['id'];
                });

                // ساختاردهی به مقالات
                foreach ($relatedArticles as $article) {
                    $articleNode = [
                        'id' => 'article_' . $article['id'],
                        'text' => $article['TITLE2'],
                        'icon' => 'fa fa-circle text-warning',
                        'a_attr' => [
                            'href' => '#article-' . $article['id'],
                            'data-price' => $article['crm_price'],
                            'data-inventory' => $article['inventory']
                        ],
                        'state' => [
                            'opened' => false,
                            'selected' => false
                        ]
                    ];

                    $groupNode['children'][] = $articleNode;
                }

                $mainNode['children'][] = $groupNode;
            }

            $tree[] = $mainNode;
        }

        return $tree;
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


    private function getImagePath($html)
    {
        if (!$html) {
            return [];
        }

        $doc = new DOMDocument();
        libxml_use_internal_errors(true); // خاموش کردن خطاها برای HTML نامنظم
        $doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $doc->getElementsByTagName('img');
        $image_links = [];

        foreach ($images as $img) {
            if ($img->hasAttribute('src')) {
                $image_links[] = $img->getAttribute('src');
            }
        }

        return $image_links;
    }

    public function A_ajax_remove_file(Request $request)
    {
        if($request->ajax()){
            $request->validate([
                "id" => "required|integer",
            ]);
            try {
                Files::findOrFail($request->get("id"))->delete();
                return response([
                    "status" => "success",
                    "message" => "حذف با موفقیت انجام شد",
                    "data"=> ''
                ]);
            }catch (\Exception $e){
                return response([
                    "status" => "error",
                    "message" => "خطایی در حذف رخ داده.",
                    "data"=> $e->getMessage()
                ]);
            }
        }
    }
    public function A_ajax_image_uploader(Request $request)
    {
        try {
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'id' => 'nullable|numeric',
            ]);

            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'jpg') {
                    $type = 'jpg';
                } elseif ($extension == 'png') {
                    $type = 'png';
                } elseif ($extension == 'webp') {
                    $type = 'webp';
                } elseif ($extension == 'jpeg') {
                    $type = 'jpeg';
                } elseif ($extension == 'gif') {
                    $type = 'gif';
                } elseif ($extension == 'webp') {
                    $type = 'webp';
                } else {
                    return redirect()->back()->with('error', 'فرمت فایل نامعتبر است');
                }


                $filename = time() . '_' . uniqid() . '.' . $type;
                $month = now()->format('Y-m');
                $path = "post/{$month}";
                $url = Storage::disk('public')->putFileAs($path, $file, $filename);

                $image = Image::create([
                    'image_url' => "{$path}/{$filename}",
                    'imageable_id' => $request->input('id', null),
                    'imageable_type' => Post::class,
                    'primary' => 99,
                    'alt' => '',
                    'status' => 1,
                ]);

                if ($url and $image) {
                    return response()->json([
                        'data' => route('get_post_image_by_id', ['id' => $image->id]),
                        'status' => 'success',
                        'message' => 'تصویر ذخیره شد.'
                    ]);

                } else {
                    return response()->json([
                        'data' => '',
                        'status' => 'error',
                        'message' => 'خطا در ذخیره سازی'
                    ]);
                }
            } else {
                return response()->json([
                    'data' => '',
                    'status' => 'error',
                    'message' => 'فایلی ارسال نشده است.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'data' => '',
                'status' => 'error',
                'message' => 'خطا در آپلود فایل: ' . $e->getMessage()
            ]);
        }
    }

    public function get_post_image_by_id($id)
    {
        $file1 = Image::find($id);

        if ($file1) {
            return Storage::disk('public')->response($file1->image_url);
        } else {
            return 1;
        }

    }
    public function get_file_by_id($id)
    {
        $file1 = Files::find($id);

        if ($file1) {
            return Storage::disk('public')->response($file1->file_url);
        } else {
            return 1;
        }

    }



}
