<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml in the public directory using Spatie\Sitemap
     */
    public function generate()
    {
        try {
            $sitemap = Sitemap::create();

            // Add homepage
            $sitemap->add(Url::create(url('/'))->setPriority(1.0));

            // Common pages by route name if available
            $commonRoutes = [
                'products', 'posts', 'about', 'contact', 'order_guide', 'frequently_asked_questions',
                'privacy', 'rules_regulations', 'return_order'
            ];

            foreach ($commonRoutes as $routeName) {
                if (Route::has($routeName)) {
                    try {
                        $sitemap->add(Url::create(route($routeName)));
                    } catch (\Exception $ex) {
                        // skip if route requires params
                    }
                }
            }

            // Products (use cursor to avoid memory issues)
            Product::where('status', 1)->whereNull('deleted_at')->cursor()->each(function (Product $product) use ($sitemap) {
                $slugOrId = $product->slug ?: $product->id;
                if (Route::has('show_product_by_id')) {
                    try {
                        $url = route('show_product_by_id', $slugOrId);
                        $sitemap->add(Url::create($url)->setLastModificationDate($product->updated_at));
                    } catch (\Exception $ex) {
                        // skip invalid
                    }
                }
            });

            // Posts
            Post::where('status', 1)->cursor()->each(function (Post $post) use ($sitemap) {
                if ($post->slug && Route::has('post')) {
                    try {
                        $url = route('post', $post->slug);
                        $sitemap->add(Url::create($url)->setLastModificationDate($post->updated_at));
                    } catch (\Exception $ex) {
                        // skip
                    }
                }
            });

            $sitemap->writeToFile(public_path('sitemap.xml'));

            return redirect()->route('A_home')->with('swal-success', 'نقشه سایت با موفقیت ساخته شد.');
        } catch (\Exception $e) {
            return redirect()->route('A_home')->with('swal-error', 'خطا هنگام ساخت نقشه سایت: ' . $e->getMessage());
        }
    }
}
