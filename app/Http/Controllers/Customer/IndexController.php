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
use App\Models\Guide_documents;

use App\Services\CartService;
use App\Services\GetDiscountService;
use App\Services\ShiliranApiInterface;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

//use Spatie\Sitemap\SitemapGenerator;

class IndexController extends Controller
{

    protected ShiliranApiInterface $api;

    public function __construct(
        ShiliranApiInterface $api,
        private readonly CartService $cartService,
    ) {
        $this->api = $api;
    }

    public function home()
    {
//        SitemapGenerator::create('http://sale')
//            ->writeToFile(public_path('sitemap.xml'));
//        dd(44);
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
            ->where('status', 1)
            ->where('new_product', 1)
            ->orderBy('created_at', 'desc')
            ->get();


        $category_banners = [
            [
                "title" => "کلید های قطع و وصل",
                "slug"  => "on-off-switches",
                // "src"   => "assets/img/banner/product-01.jpg",
                "src"   => "assets/img/banner/product-0112.webp",
            ],
            [
                "title" => "تجهیزات خورشیدی و نیروگاهی",
                "slug"  => "solar-and-power-plant-equipment",
                // "src"   => "assets/img/banner/product-02.jpg",
                 "src"   => "assets/img/banner/product-0110.webp",
            ],
            [
                "title"  => "کلید های فشاری",
                "slug" => "push-buttons",
                // "src"   => "assets/img/banner/product-03.jpg",
                 "src"   => "assets/img/banner/product-0111.webp",
            ],
            [
                "title" => "تجهیرات حفاظتی",
                "slug"  => "protective-equipment",
                // "src"   => "assets/img/banner/product-04.jpg",
                 "src"   => "assets/img/banner/product-0108.webp",
            ],
            // [
            //     "title" => "کلید اتوماتیک قابل تنظیم",
            //     "slug"  => "adjustable-automatic-switch",
            //     "src"   => "assets/img/banner/product-05.jpg",
            // ],
            // [
            //     "title" => "کلید اتوماتیک الکترونیکی",
            //     "slug"  => "electronic-automatic-key",
            //     "src"   => "assets/img/banner/product-06.jpg",
            // ],
            // [
            //     "title" => "کلید اتوماتیک فیکس",
            //     "slug"  => "automatic-key-fix",
            //     "src"   => "assets/img/banner/product-07.jpg",
            // ],
            // [
            //     "title" => "کلید هوشمند",
            //     "slug"  => "smart-electronics",
            //     "src"   => "assets/img/banner/product-08.jpg",
            // ],
            // [
            //     "title" => "تجهیزات خورشیدی",
            //     "slug"  => "solar-and-power-plant-equipment",
            //     "src"   => "assets/img/banner/product-09.jpg",
            // ],
            // [
            //     "title" => "استابلایزر",
            //     "slug"  => "stabilizer",
            //     "src"   => "assets/img/banner/product-10.jpg",
            // ],
            // [
            //     "title" => "اینورتر",
            //     "slug"  => "inverter",
            //     "src"   => "assets/img/banner/product-11.jpg",
            // ],
            // [
            //     "title" => "محافظ ولتاژ",
            //     "slug"  => "voltage-protector",
            //     "src"   => "assets/img/banner/product-12.jpg",
            // ],
        ];


        return view('app_main', compact('lastProducts', 'offerProducts', 'amazingSaleDiscount', 'category_banners'));
    }

    public function redirect_product_by_id($id)
    {
        $product = Product::select('id', 'slug')->findOrFail($id);

        return redirect()->route('product', $product->slug ?: $product->id, 301);
    }

    public function redirect_product_by_slug(string $slug)
    {
        return redirect()->route('product', trim($slug), 301);
    }

    public function show_product_by_id111111($slug)
    {
        $product = Product::with('images')
            ->where('slug', trim((string) $slug))
            ->firstOrFail();

        return $this->render_product($product);
    }

    private function render_product1111111(Product $product)
    {
        $productId = $product->id;

        $group = $this->api->getGroupById($product->group_id_in_app);

        $product_Meta = Product_attributes::where('product_id', $productId)
            ->where('meta_name', 'رنگ')
            ->first();

        $productAttributes = Product_attributes::where('product_id', $productId)->get();
        $guaranteeDuration = $this->getGuaranteeDuration($product);

        //DISCOUNT
        $discountService = new GetDiscountService();
        $discount = $discountService->getDiscount($productId);

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
            $cart_items = Cart::where('user_id', $user_id)->where('product_id', $productId)->first();
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

        $comments = Comment::Where('module', 'product')->where('process_id', $productId)->Where('status', 'approved')->get();

        $date = new Jdf();
        foreach ($comments as $comment) {
            $comment->created_at_jalali = $date->toJalali($comment->created_at);
        }


        return view('Customer.Product.product', compact('product', 'group', 'product_Meta', 'productAttributes', 'guaranteeDuration', 'postType', 'postStatus', 'comments', 'existsInCart', 'cart_items'));
    }

    public function show_product_by_id1111($slug)
    {

        $product = Product::with('images')
            ->where('slug', trim((string) $slug))
            ->firstOrFail();

        $data1 = $this->api->getInventoryByItemId($product->product_id_in_app);

        if($data1['status'] == true){
            $inventory= $data1['data'];
        }else{
            $inventory= 0;
        }

        return $this->render_product($product,$inventory);
    }

    public function show_product_by_id(string $slug)
    {
        $slug = trim($slug);

        $product = Product::query()
            ->with('images')
            ->where('slug', $slug)
            ->first();

        // اگر با id عددی باز شده باشد، در صورت داشتن slug به آدرس سئوپسند هدایت شود
        if (!$product && ctype_digit($slug)) {
            $product = Product::query()
                ->with('images')
                ->find($slug);

            if ($product && filled($product->slug) && $product->slug !== $slug) {
                return redirect()->route('product', $product->slug, 301);
            }
        }

        if (!$product) {
            abort(404);
        }

        $inventory = $this->cartService->getAvailableInventory($product);

        return $this->render_product($product, $inventory);
    }

    private function render_product(Product $product , $inventory)
    {
        $productId = $product->id;

        $mainGroupName = null;
        if (!empty($product->group_id_in_app)) {
            try {
                $group = $this->api->getGroupById((int) $product->group_id_in_app);
                if (
                    is_array($group)
                    && ($group['status'] ?? false) === true
                    && is_array($group['data'] ?? null)
                ) {
                    $mainGroupName = $group['data']['main_group_name']
                        ?? ($group['data']['name'] ?? null);
                }
            } catch (\Throwable $exception) {
                report($exception);
                $mainGroupName = null;
            }
        }

        $product_Meta = Product_attributes::where('product_id', $productId)
            ->where('meta_name', 'رنگ')
            ->first();

        $productAttributes = Product_attributes::where('product_id', $productId)->get();
        $guaranteeDuration = $this->getGuaranteeDuration($product);
        $maximumOrderLimit = $this->cartService->getMaximumOrderLimitOnSite($product);

        //DISCOUNT
        $discountService = new GetDiscountService();
        $discount = $discountService->getDiscount($productId);

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
            $cart_items = Cart::where('user_id', $user_id)->where('product_id', $productId)->first();
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

        $comments = Comment::Where('module', 'product')->where('process_id', $productId)->Where('status', 'approved')->get();

        $date = new Jdf();
        foreach ($comments as $comment) {
            $comment->created_at_jalali = $date->toJalali($comment->created_at);
        }


        return view('Customer.Product.product', compact('product', 'mainGroupName', 'product_Meta', 'productAttributes', 'guaranteeDuration', 'maximumOrderLimit', 'postType', 'postStatus', 'comments', 'existsInCart', 'cart_items','inventory'));
    }



    private function getGuaranteeDuration(Product $product): ?int
    {
        if (empty($product->product_id_in_app)) {
            return null;
        }

        try {
            $itemData = $this->api->getItemById((int) $product->product_id_in_app);

            if (($itemData['status'] ?? false) !== true) {
                return null;
            }

            $duration = $itemData['data']['guarantee_duration'] ?? null;

            if ($duration === null || $duration === '') {
                return null;
            }

            $duration = (int) $duration;

            return $duration > 0 ? $duration : null;
        } catch (\Throwable $exception) {
            report($exception);
            return null;
        }
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



    

    public function product_guide(Request $request)
    {

        $group = (string)$request->query('group') ?? null;
        $agent = (string)$request->query('agent', '');
        $main_group = (string)$request->query('main_group', '');
        $product = (string)$request->query('product', '');


        if(!$group){
           $group = null;
        }
        if(!$agent){
            $agent = null;
        }
        if(!$main_group){
            $main_group = null;
        }
        if(!$product){
            $product = null;
        }

        $guide_documents=Guide_documents::
              where('group_id', $group)
            ->where('product_id', $product)
            ->where('main_group_id', $main_group)
            ->where('agent',$agent)
            ->get();

        $guide_subject_name = $this->resolveGuideSubjectName($product, $group, $main_group);

        if ($group === '75' && $agent === '0040') {
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }elseif ($product === '2429' && $agent === '0040'){
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }elseif ($group === '203' && $agent === '0040'){
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }
        elseif($main_group === '4300' && $agent === '0041'){
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }elseif($main_group === '4400' && $agent === '0041'){
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }elseif($main_group === '4500' && $agent === '0041'){
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }elseif($group === '104' && $agent === '0034'){
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }elseif($main_group === '4200' && $agent === '0041'){
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }elseif($main_group === '5100'){
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }elseif($group === '87'){
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }elseif($group === '86'){
            return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        }
        // elseif($product === '2521'){
        //     return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));
        // }

        return view('Customer.Guide.product_guide_support',compact('guide_documents', 'guide_subject_name'));


    }

    private function resolveGuideSubjectName(?string $product, ?string $group, ?string $mainGroup): ?string
    {
        try {
            if ($product) {
                $name = Product::query()
                    ->where('product_id_in_app', $product)
                    ->value('product_name');
                if (is_string($name) && $name !== '') {
                    return $name;
                }
                if (ctype_digit($product)) {
                    $res = $this->api->getItemById((int) $product);
                    if (($res['status'] ?? false) === true && is_array($res['data'] ?? null)) {
                        $apiName = $res['data']['TITLE2'] ?? ($res['data']['title'] ?? null);
                        if (is_string($apiName) && $apiName !== '') {
                            return $apiName;
                        }
                    }
                }

                return null;
            }

            if ($group) {
                $name = Category::query()
                    ->where('type', 'product')
                    ->where('app_group_type', 'group')
                    ->where('app_id', $group)
                    ->value('name');
                if (is_string($name) && $name !== '') {
                    return $name;
                }
                if (ctype_digit($group)) {
                    $res = $this->api->getGroupById((int) $group);
                    if (($res['status'] ?? false) === true && is_array($res['data'] ?? null)) {
                        $apiName = $res['data']['title'] ?? ($res['data']['name'] ?? null);
                        if (is_string($apiName) && $apiName !== '') {
                            return $apiName;
                        }
                    }
                }

                return null;
            }

            if ($mainGroup) {
                $name = Category::query()
                    ->where('type', 'product')
                    ->where('app_group_type', 'main')
                    ->where('app_id', $mainGroup)
                    ->value('name');
                if (is_string($name) && $name !== '') {
                    return $name;
                }
                if (ctype_digit($mainGroup)) {
                    $res = $this->api->getMainGroupById((int) $mainGroup);
                    if (($res['status'] ?? false) === true && is_array($res['data'] ?? null)) {
                        $apiName = $res['data']['name'] ?? null;
                        if (is_string($apiName) && $apiName !== '') {
                            return $apiName;
                        }
                    }
                }
            }
        } catch (\Throwable $exception) {
            report($exception);
        }

        return null;
    }
}
