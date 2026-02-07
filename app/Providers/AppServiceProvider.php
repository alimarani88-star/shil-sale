<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\ShiliranApiInterface;
use App\Services\ShiliranApiService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ShiliranApiInterface::class, ShiliranApiService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['Customer.Layout.header', 'Customer.Layout.navbar','Customer.Product.products'], function ($view) {
            $mainMenuItems = Cache::remember('categories_menu', 3600, function () {
                return Category::getTreeProduct();
            });
            $view->with('categories', $mainMenuItems);
        });
    }
}
