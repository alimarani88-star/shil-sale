<?php

namespace App\Http\Controllers\Customer;

use App\CustomClass\Jdf;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use App\Models\Post_type;
use App\Models\User;
use App\Services\ShiliranApiService;
use Illuminate\Http\Request;

class PostClientController extends Controller
{
    public function posts(Request $request)
    {


        $orderBy = $request->input('orderBy', 'publish');
        $sortField = $orderBy === 'view' ? 'view' : 'published_at';


        $sort = $request->input('sort', 'desc');
        $sortDirection = strtolower($sort) === 'asc' ? 'asc' : 'desc';

        $category_slug =  $request->input('cat', '');

        $post_ids = [];
        $category_name='';
        $category = Category::where('slug', $category_slug)->first();
        if ($category) {
            $category_id = $category->id;
            $category_name = $category->name;
            $post_ids = Post_type::where('process_id', $category_id)
                ->pluck('post_id')
                ->toArray();
        }

        $posts_q = Post::where('type', 'category')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->where('expired_at', '>', now())
                    ->orWhereNull('expired_at');
            });
        $all_post_count = $posts_q->count();

        $posts = $posts_q->when(!empty($post_ids), function ($query) use ($post_ids) {
            $query->whereIn('id', $post_ids);
        })
            ->with(['tags', 'categories', 'main_image'])
            ->orderBy($sortField, $sortDirection)
            ->paginate(10) ;



        $posts->appends($request->only(['orderBy', 'sort']));

        $jdf = new Jdf();
        foreach ($posts as &$post) {
            $post->main_category = collect($post->categories)
                ->firstWhere('primary', 1)
                ?->process_name;
            $jDate = $post->published_at ? $jdf->toJalali(substr($post->published_at, 0, 10)) : '';
            $post->published = $post->published_at ? $this->convertToPersianDate($jDate) : '';
            $post->summary = mb_substr($post->summary, 0, 100 , 'utf-8');
        }

        $current_categories = Post_type::where('type', 'category')
            ->whereHas('post', function($query) {
                $query->where(function($q) {
                    $q->where('status', 'published') ;
                })
                    ->where('published_at', '<=', now())
                    ->where('status', 'published');
            })
            ->get()
            ->groupBy('process_id');
        foreach ($current_categories as &$_category) {
            $cat = Category::find($_category[0]->process_id);
            $_category[0]->slug = $cat ? $cat->slug : '';
        }
        return view('Customer.Post.posts', compact('posts' , 'current_categories' , 'category_name' , 'all_post_count'));
    }

    public function post(Request $request , $slug)
    {
        $post = Post::where('slug', $slug)->first();

        if (!$post) {
            return view('Customer.Post.not-found-post');
        } else {
            if($post->type != 'category') {
                die('درخواست شما مناسب این قالب نیست');
            }
            $sessionKey = 'post_viewed_' . $post->id;
            if (!$request->session()->has($sessionKey)) {
                $post->increment('view');
                $request->session()->put($sessionKey, true);
            }
            $tags = $post->tags->pluck('name', 'id')->toArray();
            $post_categories_q = $post->categories->where('type', 'category');
            $categories = $post_categories_q->pluck('process_name', 'id')->toArray();

            if($post->type == 'category') {
                $category_main = $post_categories_q->where('primary' , 1)->first();

                if(!$category_main){  // اگر گروه اصلی ست نشده باشد اولین گروه را به عنوان اصلی میگیرد
                    $category_main = $post_categories_q->first();
                }
                $category = Category::find($category_main?->process_id);
                $category_main->slug = $category ? $category->slug : '';
            }



            $parents = $category->getParents();
            $parents[] = $category->id;
            $breadcrumb = array_map(function ($cat_id){
                $cat = Category::find($cat_id) ;
                return [ $cat->name , $cat->slug];
            } , $parents);
            $main_image = $post->main_image;

            $jdf = new Jdf();
            $jDate = $post->published_at ? $jdf->toJalali(substr($post->published_at, 0, 10)) : '';
            $post->published = $post->published_at ? $this->convertToPersianDate($jDate) : '';


            $posts_q = Post::where('type', '<>', 'product')
                ->where('status', 'published')
                ->where(function ($query) {
                    $query->where('expired_at', '>', now())
                        ->orWhereNull('expired_at');
                });

            // سایدبار - دسته بندی هایی که محتوا دارند
            $current_categories = Post_type::where('type', 'category')
                ->whereHas('post', function($query) {
                    $query->where(function($q) {
                        $q->where('status', 'published') ;
                    })
                        ->where('published_at', '<=', now())
                        ->where('status', 'published');
                })
                ->get()
                ->groupBy('process_id');
            foreach ($current_categories as &$_category) {
                $cat = Category::find($_category[0]->process_id);
                $_category[0]->slug = $cat ? $cat->slug : '';
            }

            //  پست های مرتبط
            $post_type__related_ids = Post_type::where('type', 'category')
                ->whereIn('process_id', $parents)->get()->pluck('post_id')->toArray();

             $related_post= Post::where('type', '<>', 'product')
                ->where('status', 'published')
                 ->where('id', '<>', $post->id)
                ->where(function ($query) {
                    $query->where('expired_at', '>', now())
                        ->orWhereNull('expired_at');
                })->when(!empty($post_type__related_ids), function ($query) use ($post_type__related_ids) {
                $query->whereIn('id', $post_type__related_ids);
            })
                 ->limit(10)
                 ->get() ;

             if(sizeof($related_post) == 0 ){ // اگر پست های هم دسته نبود بر اساس بیشترین بازدید نشان بده
                 $related_post = Post::where('type', '<>', 'product')
                     ->where('status', 'published')
                     ->where('id', '<>', $post->id)
                     ->where(function ($query) {
                         $query->where('expired_at', '>', now())
                             ->orWhereNull('expired_at');
                     })
                     ->orderBy('view', 'desc')
                     ->limit(10)
                     ->get();
             }

//            comments
            $comments = $post->comments->where('status', 'approved');

            $commentTree = [];
            $processedComments = [];

// ابتدا همه کامنت‌ها را پردازش کنید
            foreach ($comments as $comment) {
                $pDate = $jdf->toJalali($comment->created_at);
                $comment->created = $this->convertToPersianDate($pDate);

                $user = User::find($comment->user_id);
                if ($user) {
                    $user_avatar = Image::where('imageable_id', $user->id)
                        ->where('imageable_type', User::class)
                        ->where('primary', 1)->first();
                    $comment->user_avatar = $user_avatar->id ?? 'assets/img/no-image.jpg';
                    $comment->user_avatar_alt = $user_avatar?->alt;
                } else {
                    $comment->user_avatar = 'assets/img/no-image.jpg';
                }

                // استفاده از متد setAttribute برای اضافه کردن replies
                $comment->setAttribute('replies', []);
                $processedComments[$comment->id] = $comment;
            }

// حالا ساختار درختی ایجاد کنید
            foreach ($processedComments as $comment) {
                if ($comment->reply) {
                    // اضافه کردن به کامنت پدر
                    if (isset($processedComments[$comment->reply])) {
                        $replies = $processedComments[$comment->reply]->replies;
                        $replies[] = $comment;
                        $processedComments[$comment->reply]->setAttribute('replies', $replies);
                    }
                }
            }

// فقط کامنت‌های اصلی (بدون parent) را جمع‌آوری کنید
            $commentTree = array_filter($processedComments, function($comment) {
                return !$comment->reply;
            });

// مرتب‌سازی بر اساس تاریخ (اختیاری)
            $commentTree = array_values($commentTree);

            $comments = array_values($commentTree);
            return view('Customer.Post.single-post', compact('post', 'tags', 'categories', 'main_image', 'breadcrumb', 'category_main', 'current_categories','related_post' , 'comments'));
        }

    }

//    متد زیر فعلا بلا استفاده
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $query = trim($query);
        $query = preg_replace('/[\x00-\x1F\x7F]/u', '', $query);
        $query = strip_tags($query);
        $query = htmlentities($query);
        $query = strip_tags($query);

        $posts = Post::query()
            ->where('status', 'published')
            ->where(function ($query) {
                $query->where('expired_at', '>', now())
                    ->orWhereNull('expired_at');
            })
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->with(['categories', 'tags'])
            ->paginate(2);
        $posts->appends($request->only(['orderBy', 'sort', 'q']));
        $jdf = new Jdf();
        foreach ($posts as &$post) {
            $post->main_category = collect($post->categories)
                ->firstWhere('primary', 1)
                ?->process_name;
            $jDate = $post->published_at ? $jdf->toJalali(substr($post->published_at, 0, 10)) : '';
            $post->published = $post->published_at ? $this->convertToPersianDate($jDate) : '';

            $text1 = $post->summary ?: null;
            $text2 = $post->content ?: null;
            $position1 = mb_stripos($text1, $query, 0, 'utf-8');
            $position2 = mb_stripos($text2, $query, 0, 'utf-8');

            if ($position1 !== false) {
                $start = max(0, $position1 - 50);
                $length = mb_strlen($query, 'utf-8') + 100;
                $excerpt = mb_substr($text1, $start, $length, 'utf-8');
                if ($start > 0) {
                    $excerpt = '...' . $excerpt;
                }
                if ($start + $length < mb_strlen($text1, 'utf-8')) {
                    $excerpt .= '...';
                }
                $post->summary = $excerpt;
            }elseif ($position2 !== false){
                $start = max(0, $position2 - 50);
                $length = mb_strlen($query, 'utf-8') + 100;
                $excerpt = mb_substr($text2, $start, $length, 'utf-8');
                if ($start > 0) {
                    $excerpt = '...' . $excerpt;
                }
                if ($start + $length < mb_strlen($text2, 'utf-8')) {
                    $excerpt .= '...';
                }
                $post->summary = $excerpt;
            }
            else {
                $post->summary = mb_substr($text1, 0, 100, 'utf-8') . '...';
            }
        }

        return view('Customer.Post.post-search', compact('posts', 'query'));
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

    public function product_guide(Request $request ,$idOrSlug)
    {

        if(is_numeric($idOrSlug)){
            $post_id = 0 ;
            $post_type_article =  Post_type::where('process_id', $idOrSlug)
                ->where('type', 'guide_article')->first();
            if ($post_type_article) {
                $post_id = $post_type_article?->post_id;
            }
            else{
                $app_service = new ShiliranApiService();
                $itemData =$app_service->getItemById($idOrSlug);
                if($itemData['status']){
                    $groupId = $itemData['data']['GROUPID'];
                    $post_type_group = Post_type::where('process_id', $groupId)
                        ->where('type', 'guide_group')->first();
                    if($post_type_group){
                        $post_id = $post_type_group?->post_id;
                    }else{
                        $groupData = $app_service->getGroupById($groupId);
                        if($groupData['status']){
                            $main_group_id = $groupData['data']['main_group_id'];
                            $post_type_main = Post_type::where('process_id', $main_group_id)
                                ->where('type', 'guide_main')->first();
                            if($post_type_main){
                                $post_id = $post_type_main?->post_id;
                            }
                        }
                    }

                }
            }

            $post = Post::find($post_id);
        }else{
            $post = Post::where('slug', $idOrSlug)->first();
        }

        if (!$post) {
            return view('Customer.Post.not-found-post');
        } else {
            if($post->type != 'guide') {
                die('درخواست شما مناسب این قالب نیست');
            }
            $sessionKey = 'post_viewed_' . $post->id;
            if (!$request->session()->has($sessionKey)) {
                $post->increment('view');
                $request->session()->put($sessionKey, true);
            }
            $tags = $post->tags->pluck('name', 'id')->toArray();

            $main_image = $post->main_image;


            $customFields = $post->custom_fields ? unserialize($post->custom_fields) : [];
            // گروه‌بندی بر اساس نوع فیلد
            $groupedFields = [];
            foreach ($customFields as $field) {
                $type = $field['field_type'] ?? 'other';
                $groupedFields[$type][] = $field;
            }

            $post->groupedFields = $groupedFields ;
//            dd($post->groupedFields);

            $comments = $post->comments->where('status', 'approved');

            $commentTree = [];
            $processedComments = [];

            $jdf = new Jdf();
// ابتدا همه کامنت‌ها را پردازش کنید
            foreach ($comments as $comment) {
                $pDate = $jdf->toJalali($comment->created_at);
                $comment->created = $this->convertToPersianDate($pDate);

                $user = User::find($comment->user_id);
                if ($user) {
                    $user_avatar = Image::where('imageable_id', $user->id)
                        ->where('imageable_type', User::class)
                        ->where('primary', 1)->first();
                    $comment->user_avatar = $user_avatar->id ?? 'assets/img/no-image.jpg';
                    $comment->user_avatar_alt = $user_avatar?->alt;
                } else {
                    $comment->user_avatar = 'assets/img/no-image.jpg';
                }

                // استفاده از متد setAttribute برای اضافه کردن replies
                $comment->setAttribute('replies', []);
                $processedComments[$comment->id] = $comment;
            }

// حالا ساختار درختی ایجاد کنید
            foreach ($processedComments as $comment) {
                if ($comment->reply) {
                    // اضافه کردن به کامنت پدر
                    if (isset($processedComments[$comment->reply])) {
                        $replies = $processedComments[$comment->reply]->replies;
                        $replies[] = $comment;
                        $processedComments[$comment->reply]->setAttribute('replies', $replies);
                    }
                }
            }

// فقط کامنت‌های اصلی (بدون parent) را جمع‌آوری کنید
            $commentTree = array_filter($processedComments, function($comment) {
                return !$comment->reply;
            });

// مرتب‌سازی بر اساس تاریخ (اختیاری)
            $commentTree = array_values($commentTree);

            $comments = array_values($commentTree);

            return view('Customer.Guide.product_guide', compact('post', 'tags',  'main_image' , 'comments'));
        }

    }
}
