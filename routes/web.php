<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\IndexController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\Customer\UserProfileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


//Route::get('/', function () {return Inertia::render('welcome');})->name('home');

Route::get('/', [IndexController::class, 'home'])->name('home');
Route::get('/show_product_by_id/{id}', [IndexController::class, 'show_product_by_id'])->name('show_product_by_id');


// products routes
Route::get('/products', [\App\Http\Controllers\Customer\ProductClientController::class, 'products'])->name('products');

Route::get('/products_category/{slug}', [\App\Http\Controllers\Customer\ProductClientController::class, 'products_category'])->name('products_category');
Route::get('/ajax_load_products', [\App\Http\Controllers\Customer\ProductClientController::class, 'ajax_load_products'])->name('ajax_load_products');


Route::get('/ajax_main_header_search', [IndexController::class, 'ajax_main_header_search'])->name('ajax_main_header_search');

Route::get('/offline', [IndexController::class, 'offline'])->name('offline');



Route::middleware(['auth', 'verified', 'AccessUser'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    Route::get('/A_home', [HomeController::class, 'A_home'])->name('A_home');


    //product
    Route::get('/A_show_product', [ProductController::class, 'A_show_product'])->name('A_show_product');
    Route::get('/A_create_product', [ProductController::class, 'A_create_product'])->name('A_create_product');
    Route::get('/ajax_A_create_product', [ProductController::class, 'ajax_A_create_product'])->name('ajax_A_create_product');
    Route::post('/A_s_create_product', [ProductController::class, 'A_s_create_product'])->name('A_s_create_product');
    Route::get('/A_edit_product/{product}', [ProductController::class, 'A_edit_product'])->name('A_edit_product');
    Route::put('/A_s_edit_product/{product}', [ProductController::class, 'A_s_edit_product'])->name('A_s_edit_product');
    Route::delete('/A_inactive_product/{product}', [ProductController::class, 'A_inactive_product'])->name('A_inactive_product');


    //Discount
    Route::get('/A_show_discount', [DiscountController::class, 'A_show_discount'])->name('A_show_discount');
    Route::get('/A_create_discount', [DiscountController::class, 'A_create_discount'])->name('A_create_discount');
    Route::post('/A_s_create_discount', [DiscountController::class, 'A_s_create_discount'])->name('A_s_create_discount');
    Route::get('/A_edit_discount/{discount}', [DiscountController::class, 'A_edit_discount'])->name('A_edit_discount');
    Route::put('/A_s_edit_discount/{discount}', [DiscountController::class, 'A_s_edit_discount'])->name('A_s_edit_discount');
    Route::delete('/A_inactive_discount/{discount}', [DiscountController::class, 'A_inactive_discount'])->name('A_inactive_discount');

    Route::get('/A_show_amazingsale', [DiscountController::class, 'A_show_amazingsale'])->name('A_show_amazingsale');
    Route::get('/A_show_common_discount', [DiscountController::class, 'A_show_common_discount'])->name('A_show_common_discount');
    Route::get('/A_create_amazingsale', [DiscountController::class, 'A_create_amazingsale'])->name('A_create_amazingsale');
    Route::get('/A_create_common_discount', [DiscountController::class, 'A_create_common_discount'])->name('A_create_common_discount');
    Route::post('/A_s_create_amazingsale', [DiscountController::class, 'A_s_create_amazingsale'])->name('A_s_create_amazingsale');
    Route::post('/A_s_create_common_discount', [DiscountController::class, 'A_s_create_common_discount'])->name('A_s_create_common_discount');
    Route::get('/A_edit_amazingsale/{id}', [DiscountController::class, 'A_edit_amazingsale'])->name('A_edit_amazingsale');
    Route::put('/A_s_edit_amazingsale/{id}', [DiscountController::class, 'A_s_edit_amazingsale'])->name('A_s_edit_amazingsale');
    Route::get('/A_edit_common_discount/{id}', [DiscountController::class, 'A_edit_common_discount'])->name('A_edit_common_discount');
    Route::put('/A_s_edit_common_discount/{id}', [DiscountController::class, 'A_s_edit_common_discount'])->name('A_s_edit_common_discount');
    Route::delete('/A_inactive_amazingsale/{id}', [DiscountController::class, 'A_inactive_amazingsale'])->name('A_inactive_amazingsale');
    Route::delete('/A_inactive_common_discount/{id}', [DiscountController::class, 'A_inactive_common_discount'])->name('A_inactive_common_discount');


    //admin post
    Route::get('/A_create_post', [\App\Http\Controllers\Admin\PostController::class, 'A_create_post'])->name('A_create_post');
    Route::get('/A_edit_post/{id}', [\App\Http\Controllers\Admin\PostController::class, 'A_edit_post'])->name('A_edit_post');
    Route::post('/A_s_create_post', [\App\Http\Controllers\Admin\PostController::class, 'A_s_create_post'])->name('A_s_create_post');
    Route::post('/A_s_edit_post/{id}', [\App\Http\Controllers\Admin\PostController::class, 'A_s_edit_post'])->name('A_s_edit_post');
    Route::post('/A_s_status_post/{id_status}', [\App\Http\Controllers\Admin\PostController::class, 'A_s_status_post'])->name('A_s_status_post');
    Route::get('/A_posts', [\App\Http\Controllers\Admin\PostController::class, 'A_posts'])->name('A_posts');
    Route::get('/A_ajax_get_posts', [\App\Http\Controllers\Admin\PostController::class, 'A_ajax_get_posts'])->name('A_ajax_get_posts');
    Route::post('/A_ajax_image_uploader', [\App\Http\Controllers\Admin\PostController::class, 'A_ajax_image_uploader'])->name('A_ajax_image_uploader');
    Route::post('/ajax_check_slug_title', [\App\Http\Controllers\Admin\PostController::class, 'ajax_check_slug_title'])->name('ajax_check_slug_title');
    Route::post('/A_ajax_remove_image', [\App\Http\Controllers\Admin\PostController::class, 'A_ajax_remove_image'])->name('A_ajax_remove_image');


    // category
    Route::get('/A_create_category', [\App\Http\Controllers\Admin\CategoryController::class, 'A_create_category'])->name('A_create_category');
    Route::get('/A_edit_category/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'A_edit_category'])->name('A_edit_category');
    Route::post('/A_s_create_category', [\App\Http\Controllers\Admin\CategoryController::class, 'A_s_create_category'])->name('A_s_create_category');
    Route::post('/A_s_edit_category/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'A_s_edit_category'])->name('A_s_edit_category');
    Route::get('/A_categories', [\App\Http\Controllers\Admin\CategoryController::class, 'A_categories'])->name('A_categories');
    Route::get('/A_parents_categories', [\App\Http\Controllers\Admin\CategoryController::class, 'A_parents_categories'])->name('A_parents_categories');
    Route::get('/A_show_category_tree', [\App\Http\Controllers\Admin\CategoryController::class, 'A_show_category_tree'])->name('A_show_category_tree');


    // client comment
    Route::post('/create_comment', [\App\Http\Controllers\Customer\ClientCommentController::class, 'create_comment'])->name('create_comment');


    //setting
    Route::get('/A_setting', [SettingController::class, 'A_setting'])->name('A_setting');
    Route::get('/A_edit_about', [SettingController::class, 'A_edit_about'])->name('A_edit_about');
    Route::get('/A_edit_contact', [SettingController::class, 'A_edit_contact'])->name('A_edit_contact');


    Route::get('/A_edit_frequently_asked_questions', [SettingController::class, 'A_edit_frequently_asked_questions'])->name('A_edit_frequently_asked_questions');
    Route::post('/A_s_edit_frequently_asked_questions', [SettingController::class, 'A_s_edit_frequently_asked_questions'])->name('A_s_edit_frequently_asked_questions');
    Route::post('/A_delete_frequently_asked_questions', [SettingController::class, 'A_delete_frequently_asked_questions'])->name('A_delete_frequently_asked_questions');
    Route::put('/A_update_frequently_asked_questions/{id}', [SettingController::class, 'A_update_frequently_asked_questions'])->name('A_update_frequently_asked_questions');


    Route::post('/A_s_edit_company_info', [SettingController::class, 'A_s_edit_company_info'])->name('A_s_edit_company_info');
    Route::post('/A_s_edit_about', [SettingController::class, 'A_s_edit_about'])->name('A_s_edit_about');

    //reports
    Route::get('/A_report_of_exhibition_customers', [ReportController::class, 'A_report_of_exhibition_customers'])->name('A_report_of_exhibition_customers');
    Route::get('/A_report_of_site_customers', [ReportController::class, 'A_report_of_site_customers'])->name('A_report_of_site_customers');
    Route::get('/A_report_of_per_customer/{id}', [ReportController::class, 'A_report_of_per_customer'])->name('A_report_of_per_customer');
    Route::get('/A_report_of_exhibition_visitors', [ReportController::class, 'A_report_of_exhibition_visitors'])->name('A_report_of_exhibition_visitors');
    Route::get('/A_report_of_exhibition_visitors_by_city', [ReportController::class, 'A_report_of_exhibition_visitors_by_city'])->name('A_report_of_exhibition_visitors_by_city');

    // packing
    Route::get('/A_product_packing_list', [\App\Http\Controllers\Admin\PackingController::class, 'A_product_packing_list'])->name('A_product_packing_list');
    Route::get('/A_post_packing_list', [\App\Http\Controllers\Admin\PackingController::class, 'A_post_packing_list'])->name('A_post_packing_list');
    Route::get('/ajax_fetch_packing', [\App\Http\Controllers\Admin\PackingController::class, 'ajax_fetch_packing'])->name('ajax_fetch_packing');
    Route::get('/ajax_save_packing', [\App\Http\Controllers\Admin\PackingController::class, 'ajax_save_packing'])->name('ajax_save_packing');
    Route::get('/ajax_product_packing_list', [\App\Http\Controllers\Admin\PackingController::class, 'ajax_product_packing_list'])->name('ajax_product_packing_list');
    Route::get('/A_delete_packing/{id}', [\App\Http\Controllers\Admin\PackingController::class, 'A_delete_packing'])->name('A_delete_packing');
    Route::get('/A_post_packing_list', [\App\Http\Controllers\Admin\PackingController::class, 'A_post_packing_list'])->name('A_post_packing_list');


});

//customer
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/reset_password', [UserProfileController::class, 'reset_password'])->name('reset_password');
    Route::post('/s_reset_password', [UserProfileController::class, 's_reset_password'])->name('s_reset_password');

    //customer-comment
    Route::post('/C_add_comment/{id}', [IndexController::class, 'C_add_comment'])->name('C_add_comment');


    //customer-profile
    Route::get('/C_profile', [UserProfileController::class, 'C_profile'])->name('C_profile');
    Route::get('/edit_profile', [UserProfileController::class, 'edit_profile'])->name('edit_profile');
    Route::get('/profile_orders', [UserProfileController::class, 'profile_orders'])->name('profile_orders');
    Route::get('/profile_orders_return', [UserProfileController::class, 'profile_orders_return'])->name('profile_orders_return');
    Route::get('/profile_favorites', [UserProfileController::class, 'profile_favorites'])->name('profile_favorites');
    Route::get('/profile_personal_info', [UserProfileController::class, 'profile_personal_info'])->name('profile_personal_info');
    Route::post('/s_edit_profile', [UserProfileController::class, 's_edit_profile'])->name('s_edit_profile');
    Route::get('/profile_addresses', [UserProfileController::class, 'profile_addresses'])->name('profile_addresses');
    Route::get('/profile_add_address', [UserProfileController::class, 'profile_add_address'])->name('profile_add_address');
    Route::post('/s_profile_add_address', [UserProfileController::class, 's_profile_add_address'])->name('s_profile_add_address');
    Route::get('/profile_edit_address', [UserProfileController::class, 'profile_edit_address'])->name('profile_edit_address');
    Route::post('/profile_remove_address/{id}', [UserProfileController::class, 'profile_remove_address'])->name('profile_remove_address');
    Route::get('/ajax_get_address/{id}', [UserProfileController::class, 'ajax_get_address'])->name('ajax_get_address');
    Route::post('/s_profile_edit_address/{id}', [UserProfileController::class, 's_profile_edit_address'])->name('s_profile_edit_address');
    Route::get('/order_detail', [UserProfileController::class, 'order_detail'])->name('order_detail');


    //cart
    Route::get('/cart', [CartController::class, 'cart'])->name('cart');
    Route::post('/add_product_to_cart', [CartController::class, 'add_product_to_cart'])->name('add_product_to_cart');
    Route::get('/ajax_cart_header', [CartController::class, 'ajax_cart_header'])->name('ajax_cart_header');
    Route::post('/update_count_product_cart', [CartController::class, 'update_count_product_cart'])->name('update_count_product_cart');
    Route::post('/remove_from_cart', [CartController::class, 'remove_from_cart'])->name('remove_from_cart');
    Route::get('/cart_select_address', [CartController::class, 'cart_select_address'])->name('cart_select_address');
    Route::post('/cart_select_payment_type', [CartController::class, 'cart_select_payment_type'])->name('cart_select_payment_type');
    Route::get('/cart_payment', [CartController::class, 'cart_payment'])->name('cart_payment');
    Route::post('/ajax_change_address_default_cart', [CartController::class, 'ajax_change_address_default_cart'])->name('ajax_change_address_default_cart');

    //remove-from-favorite
    Route::post('/remove_from_favorites/{id}', [UserProfileController::class, 'remove_from_favorites'])->name('remove_from_favorites');




});

//add_to_favorites
Route::post('/add_to_favorites/{id}', [IndexController::class, 'add_to_favorites'])->name('add_to_favorites');


// client post
Route::get('/posts', [\App\Http\Controllers\Customer\PostClientController::class, 'posts'])->name('posts');
Route::get('/post/{slug}', [\App\Http\Controllers\Customer\PostClientController::class, 'post'])->name('post');
Route::get('/search', [\App\Http\Controllers\Customer\PostClientController::class, 'search'])->name('search');

//about
Route::get('/about', [CompanyInfoController::class, 'about'])->name('about');
Route::get('/contact', [CompanyInfoController::class, 'contact'])->name('contact');

Route::get('/order_guide', [CompanyInfoController::class, 'order_guide'])->name('order_guide');
Route::get('/frequently_asked_questions', [CompanyInfoController::class, 'frequently_asked_questions'])->name('frequently_asked_questions');
Route::get('/privacy', [CompanyInfoController::class, 'privacy'])->name('privacy');
Route::get('/rules_regulations', [CompanyInfoController::class, 'rules_regulations'])->name('rules_regulations');
Route::get('/return_order', [CompanyInfoController::class, 'return_order'])->name('return_order');


Route::get('/get_image_by_id/{id}', [ProductController::class, 'get_image_by_id'])->name('get_image_by_id');
Route::get('/get_post_image_by_id/{id}', [\App\Http\Controllers\Admin\PostController::class, 'get_post_image_by_id'])->name('get_post_image_by_id');
Route::get('/get_file_by_id/{id}', [\App\Http\Controllers\Admin\PostController::class, 'get_file_by_id'])->name('get_file_by_id');
Route::post('/A_ajax_remove_file', [\App\Http\Controllers\Admin\PostController::class, 'A_ajax_remove_file'])->name('A_ajax_remove_file');

Route::get('/mobile_number_verification', [UserProfileController::class, 'mobile_number_verification'])->name('mobile_number_verification');
Route::post('/s_mobile_number_verification', [UserProfileController::class, 's_mobile_number_verification'])->name('s_mobile_number_verification');
Route::post('/resend_verification_code', [UserProfileController::class, 'resend_verification_code'])->name('resend_verification_code');


Route::get('/forgot_password_verification', [UserProfileController::class, 'forgot_password_verification'])->name('forgot_password_verification');
Route::post('/s_forgot_password_verification', [UserProfileController::class, 's_forgot_password_verification'])->name('s_forgot_password_verification');
Route::post('/resend_forgot_password_verification', [UserProfileController::class, 'resend_forgot_password_verification'])->name('resend_forgot_password_verification');

//
Route::get('/register_customer', [UserProfileController::class, 'register_customer'])->name('register_customer');
Route::get('/ajax_register_customer', [UserProfileController::class, 'ajax_register_customer'])->name('ajax_register_customer');
Route::post('/s_register_customer', [UserProfileController::class, 's_register_customer'])->name('s_register_customer');
Route::get('/customer_links', [UserProfileController::class, 'customer_links'])->name('customer_links');


Route::get('/product_guide/{idOrSlug}', [\App\Http\Controllers\Customer\PostClientController::class, 'product_guide'])->name('product_guide');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

