<?php

namespace App\Http\Controllers\Customer;

use App\CustomClass\Jdf;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Discount;
use App\Models\DiscountProduct;
use App\Models\Favorite;
use App\Models\Post;
use App\Models\Post_type;
use App\Models\Product;
use App\Models\Product_attributes;

use App\Services\GetDiscountService;
use App\Services\ShiliranApiInterface;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{

    protected ShiliranApiInterface $api;

    public function __construct(ShiliranApiInterface $api)
    {
        $this->api = $api;
    }

    public function home()
    {
        //dd(32);
        $isLoggedIn = Auth::check();
        if ($isLoggedIn) {
            $user = Auth::user();
            if($user->type == 'user'){
                return redirect()->route('A_home');
            }
        }


        $amazingSaleDiscount = Discount::where('discount_type', 'amazingsale')
            ->active()
            ->latest()
            ->first();


        $offerProducts = collect();

        if ($amazingSaleDiscount) {
            $offerProducts = DiscountProduct::where('discount_id', $amazingSaleDiscount->id)
                ->whereNull('deleted_at')
                ->with([
                    'products.images' => function ($q) {
                        $q->where('position', 1)
                            ->whereNull('deleted_at');
                    }
                ])
                ->latest()
                ->take(8)
                ->get();
        }


        $lastProducts = Product::with('images')
            ->orderBy('created_at', 'desc')
            ->where('status', 1)
            ->whereIn('id',[84,79,34,35,87])
            ->take(5)
            ->get();


        $topFavoriteProducts = DB::table('favorites')
            ->where('favoritable_type', Product::class)
            ->select('favoritable_id', DB::raw('COUNT(*) as total'))
            ->groupBy('favoritable_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $productIds = $topFavoriteProducts->pluck('favoritable_id');

        $topProducts = Product::whereIn('id', $productIds)->with('images')->get();


        $category_banners = [
            [
                "title" => "کلید های مینیاتوری",
                "slug"  => "miniature-key",
                "src"   => "assets/img/banner/product-01.jpg",
            ],
            [
                "title" => "محافظ جان",
                "slug"  => "miniature-life-saving-key",
                "src"   => "assets/img/banner/product-02.jpg",
            ],
            [
                "title"  => "کلید کنتاکتور",
                "slug" => "contactor-key",
                "src"   => "assets/img/banner/product-03.jpg",
            ],
            [
                "title" => "تجهیزات صنعتی",
                "slug"  => "industrial-equipment",
                "src"   => "assets/img/banner/product-04.jpg",
            ],
            [
                "title" => "کلید اتوماتیک قابل تنظیم",
                "slug"  => "adjustable-automatic-switch",
                "src"   => "assets/img/banner/product-05.jpg",
            ],
            [
                "title" => "کلید اتوماتیک الکترونیکی",
                "slug"  => "electronic-automatic-key",
                "src"   => "assets/img/banner/product-06.jpg",
            ],
            [
                "title" => "کلید اتوماتیک فیکس",
                "slug"  => "automatic-key-fix",
                "src"   => "assets/img/banner/product-07.jpg",
            ],
            [
                "title" => "کلید هوشمند",
                "slug"  => "smart-electronics",
                "src"   => "assets/img/banner/product-08.jpg",
            ],
            [
                "title" => "تجهیزات خورشیدی",
                "slug"  => "solar-and-power-plant-equipment",
                "src"   => "assets/img/banner/product-09.jpg",
            ],
            [
                "title" => "استابلایزر",
                "slug"  => "stabilizer",
                "src"   => "assets/img/banner/product-10.jpg",
            ],
            [
                "title" => "اینورتر",
                "slug"  => "inverter",
                "src"   => "assets/img/banner/product-11.jpg",
            ],
            [
                "title" => "محافظ ولتاژ",
                "slug"  => "voltage-protector",
                "src"   => "assets/img/banner/product-12.jpg",
            ],
        ];


        return view('app_main', compact('lastProducts', 'offerProducts', 'amazingSaleDiscount', 'topProducts' , 'category_banners'));
    }

    public function show_product_by_id($id)
    {
//        dd($id);

        $product = Product::with('images')->findOrFail($id);

        $group = $this->api->getGroupById($product->group_id_in_app);

        $product_Meta = Product_attributes::where('product_id', $id)
            ->where('meta_name', 'رنگ')
            ->first();

        $productAttributes = Product_attributes::where('product_id', $product->id)->get();

        //DISCOUNT
        $discountService = new GetDiscountService();
        $discount = $discountService->getDiscount($product->id);

        if ($discount['status'] == 'success') {
            $product->discount_type = $discount['data']['type'];
            $product->productPercentage = $discount['data']['percentage'];
        } else {
            $product->discount_type = null;
            $product->productPercentage = null;
        }


        //POST
        $publishedPostData = $this->getPublishedPost($product);

        // check exists in cart

        $cart_items = [];

        $existsInCart = false;
        $cart_items = [];
        if (Auth::check()) {
            $user_id = Auth::id();
            $cart_items = Cart::where('user_id', $user_id)->where('product_id', $id)->first();
            if ($cart_items) {
                $existsInCart = true;
            }
        }


        if ($publishedPostData) {
            $postType = $publishedPostData['postType'];
            $postStatus = $publishedPostData['post'];
        } else {
            $postType = null;
            $postStatus = null;

        }

        //comments

        $comments = Comment::Where('module', 'product')->where('process_id', $id)->Where('status', 'approved')->get();

        $date = new Jdf();
        foreach ($comments as $comment) {
            $comment->created_at_jalali = $date->toJalali($comment->created_at);
        }


        return view('Customer.Product.product', compact('product', 'group', 'product_Meta', 'productAttributes', 'postType', 'postStatus', 'comments', 'existsInCart', 'cart_items'));
    }

    function getPublishedPost($product)
    {

        $items = [
            ['id' => $product->product_id_in_app, 'type' => 'article'],
            ['id' => $product->group_id_in_app, 'type' => 'group'],
            ['id' => $product->main_group_id_in_app, 'type' => 'main'],
        ];

        foreach ($items as $item) {
            if (!$item['id']) continue;

            $posts = Post_type::where('process_id', $item['id'])
                ->where('type', $item['type'])
                ->orderByDesc('created_at')
                ->get();

            foreach ($posts as $p) {
                $postStatus = Post::where('id', $p->post_id)
                    ->where('status', 'published')
                    ->first();

                if ($postStatus) {
                    return [
                        'postType' => $p,
                        'post' => $postStatus
                    ];

                }
            }
        }

        return null;
    }

    public function C_add_comment(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'برای ثبت نظر باید وارد سایت شوید.');
        }

        $request->validate([
            'comment' => 'required|string|min:3',
        ]);

        try {
            $alreadyCommented = Comment::where('user_id', Auth::id())
                ->where('process_id', $id)
                ->where('status', 'pending')
                ->whereDate('created_at', today())
                ->exists();

            if ($alreadyCommented) {
                return redirect()->back()->with('error', 'شما در روز جاری نظری ارسال کرده‌اید و در حال بررسی است.');
            }

            $comment = Comment::create([
                'module' => 'product',
                'process_id' => $id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'content' => strip_tags($request->comment),
                'status' => 'pending',
            ]);

            return $comment
                ? redirect()->back()->with('success', 'دیدگاه شما ثبت شد و منتظر تایید ادمین است.')
                : redirect()->back()->with('error', 'مشکلی در ثبت دیدگاه بوجود آمد.');

        } catch (\Exception $exception) {
            return redirect()->back()->with('error', 'خطای غیر منتظره لطفا دوباره تلاش کنید.');
        }
    }

    public function add_to_favorites($id)
    {
        if (!auth()->check()) {
            return response()->json(['status' => 3]);
        }

        $user = auth()->user();
        $product = Product::findOrFail($id);


        $alreadyFavorited = $product->favoritedBy()->where('user_id', $user->id)->exists();

        if ($alreadyFavorited) {

            $product->favoritedBy()->detach($user->id);
            return response()->json(['status' => 2]);
        } else {

            $product->favoritedBy()->attach($user->id);
            return response()->json(['status' => 1]);
        }
    }

    public function ajax_main_header_search(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'search' => 'required|string',
            ]);

            try {
                $search_result = [
                    "product_category" => [],
                    "post_category" => [],
                    "product" => [],
                    "post" => [],
                    "tag" => [],
                ];


                $search = $request->input('search');
                $words = preg_split('/\s+/', trim($search));

                $query = Category::where('type', 'product');
                $query->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $q->orWhere('name', 'LIKE', "%{$word}%");
                    }
                });
                $results = $query->get()->toArray();
                $search_result['product_category'] = $this->most_similar($results, $words , 10);

                $query2 = Category::where('type', 'post');
                $query2->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $q->orWhere('name', 'LIKE', "%{$word}%");
                    }
                });
                $results = $query2->get()->toArray();

                $search_result['post_category'] = $this->most_similar($results, $words , 10);


                $query3 = Product::where('status', '1');
                $query3->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $q->orWhere('product_name', 'LIKE', "%{$word}%");
                    }
                });
                $results = $query3->get()->toArray();
                $search_result['product'] = $this->most_similar($results, $words , 10);

                $query4 = Post::where('status', 'published');
                $query4->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $q->orWhere('title', 'LIKE', "%{$word}%");
                    }
                });
                $results = $query4->get()->toArray();
                $search_result['post'] = $this->most_similar($results, $words , 10);


                return response(['status' => 'success', 'message' => '', 'data' => $search_result]);
            } catch (\Exception $exception) {
                return response(['status' => 'error', 'message' => $exception->getMessage() . ' in line :' . $exception->getLine(), 'data' => '']);
            }
        } else {
            return response(['status' => 'error', 'message' => 'درخواست نامعتبر', 'data' => '']);
        }


    }


    public function most_similar($results = [], $words = [], $n = 10)
    {
        $words = array_filter(array_unique(array_map('trim', $words)));
        foreach ($results as &$item) {
            $item['name'] = $item['name']
                ?? $item['product_name']
                ?? $item['title']
                ?? '';
            $name = mb_strtolower((string) $item['name']);
            $score = 0;
            foreach ($words as $word) {
                $word = mb_strtolower($word);
                if ($word !== '' && stripos($name, $word) !== false) {
                    $score++;
                    if($word === $name){
                        $score++;
                    }
                }
            }
            $item['score'] = $score;
        }
        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        return array_slice($results, 0, $n);
    }


    public function after_sales_service($group_id=null)
    {
        return view('PublicPages.after_sales_service', compact('group_id'));
    }
}
