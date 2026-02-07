<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <div class="brand-link" style="text-decoration: none;">
        <img src="{{ asset('assets/img/logo-icon.png') }}" class="brand-image img-circle elevation-3"
             style="opacity: 2;">
        <span class="brand-text font-weight-light" style="text-decoration: none;">پنل مدیریت</span>
    </div>
    <!-- Brand Logo -->


    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="info">
                    <span class="brand-text text-white">{{ auth()->user()?->name }}</span>
                </div>
            </div>
            <div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        خروج
                    </button>
                </form>
            </div>


            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->
                    <li class="nav-item has-treeview">
                        <a href="{{route('home')}}" class="nav-link">
                            <i class="nav-icon fa fa-shopping-cart"></i>
                            <p>فروشگاه</p>
                        </a>
                    </li>


                    <li class="nav-item has-treeview {{ request()->routeIs('A_show_product', 'A_create_product') ? 'menu-open' : '' }}">
                        <a href="#"
                           class="nav-link {{ request()->routeIs('A_show_product', 'A_create_product') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-cubes"></i>
                            <p>
                                کالاها
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('A_show_product') }}"
                                   class="nav-link {{ request()->routeIs('A_show_product') ? 'active' : '' }}">
                                    <i class="fa fa-list nav-icon"></i>
                                    <p>کالاها</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('A_create_product') }}"
                                   class="nav-link {{ request()->routeIs('A_create_product') ? 'active' : '' }}">
                                    <i class="fa fa-plus-square nav-icon"></i>
                                    <p>ایجاد کالای جدید</p>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="nav-item has-treeview {{ request()->routeIs('A_show_amazingsale','A_show_common_discount','A_show_discount') ? 'menu-open' : '' }}">
                        <a href="#"
                           class="nav-link {{ request()->routeIs('A_show_amazingsale','A_show_common_discount','A_show_discount') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-tags"></i>
                            <p>
                                تخفیف ها
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('A_show_discount') }}"
                                   class="nav-link {{ request()->routeIs('A_show_discount') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-bolt"></i>
                                    <p>تخفیف ها</p>
                                </a>
                            </li>
                        </ul>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('A_show_amazingsale') }}"
                                   class="nav-link {{ request()->routeIs('A_show_amazingsale') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-bolt"></i>
                                    <p>فروش شگفت انگیز</p>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('A_show_common_discount') }}"
                                   class="nav-link {{ request()->routeIs('A_show_common_discount') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-bolt"></i>
                                    <p>تخفیف عمومی</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ request()->routeIs('A_create_post', 'A_edit_post', 'A_categories' , 'A_posts','A_create_category' , 'A_edit_category') ? 'menu-open' : '' }}">
                        <a href="#"
                           class="nav-link {{ request()->routeIs('A_create_post', 'A_edit_post', 'A_categories' , 'A_posts','A_create_category' , 'A_edit_category') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-book"></i>
                            <p>
                                پست ها
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('A_posts') }}"
                                   class="nav-link  {{ request()->routeIs('A_posts'  ) ? 'active' : '' }}">
                                    <i class="fa fa-circle-o nav-icon"></i>
                                    <p>پست ها</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('A_create_post') }}"
                                   class="nav-link  {{ request()->routeIs('A_create_post', 'A_edit_post' ) ? 'active' : '' }}">
                                    <i class="fa fa-circle-o nav-icon"></i>
                                    <p>ایجاد پست جدید</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('A_categories') }}"
                                   class="nav-link  {{ request()->routeIs('A_categories' ) ? 'active' : '' }}">
                                    <i class="fa fa-circle-o nav-icon "></i>
                                    <p>دسته بندی ها</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('A_create_category') }}"
                                   class="nav-link  {{ request()->routeIs('A_create_category' ) ? 'active' : '' }}">
                                    <i class="fa fa-circle-o nav-icon "></i>
                                    <p>ایجاد دسته بندی</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-treeview {{ request()->routeIs('A_post_packing_list', 'A_product_packing_list') ? 'menu-open' : '' }}">
                        <a href="#"
                           class="nav-link {{ request()->routeIs('A_post_packing_list', 'A_product_packing_list') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-cubes"></i>
                            <p>
                                بسته بندی
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('A_product_packing_list') }}"
                                   class="nav-link  {{ request()->routeIs('A_product_packing_list'  ) ? 'active' : '' }}">
                                    <i class="fa fa-circle-o nav-icon"></i>
                                    <p>بسته بندی محصولات</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('A_post_packing_list') }}"
                                   class="nav-link  {{ request()->routeIs('A_post_packing_list' ) ? 'active' : '' }}">
                                    <i class="fa fa-circle-o nav-icon"></i>
                                    <p>بسته بندی پست</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                    <li
                        class="nav-item has-treeview {{ request()->routeIs('A_edit_about', 'A_edit_contact') ? 'menu-open' : '' }}">
                        <a href="#"
                           class="nav-link {{ request()->routeIs('A_edit_about', 'A_edit_contact') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-cog"></i>
                            <p>
                                تنظیمات
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('A_edit_about') }}"
                                   class="nav-link {{ request()->routeIs('A_edit_about') ? 'active' : '' }}">
                                    <i class="fa fa-users nav-icon"></i>
                                    <p>درباره ما</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('A_edit_contact') }}"
                                   class="nav-link {{ request()->routeIs('A_edit_contact') ? 'active' : '' }}">
                                    <i class="fa fa-phone nav-icon"></i>
                                    <p>تماس با ما</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('A_edit_frequently_asked_questions') }}"
                                   class="nav-link {{ request()->routeIs('A_edit_frequently_asked_questions') ? 'active' : '' }}">
                                    <i class="fa fa-question-circle nav-icon"></i>
                                    <p>سوالات متداول</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-treeview
    {{ request()->routeIs(
        'A_report_of_exhibition_customers',
        'A_report_of_exhibition_visitors',
        'A_report_of_exhibition_visitors_by_city',
        'A_report_of_site_customers'
    ) ? 'menu-open' : '' }}">

                        <a href="#"
                           class="nav-link
       {{ request()->routeIs(
           'A_report_of_exhibition_customers',
           'A_report_of_exhibition_visitors',
           'A_report_of_site_customers',
           'A_report_of_exhibition_visitors_by_city'
       ) ? 'active' : '' }}">
                            <i class="fa fa-pie-chart" aria-hidden="true"></i>
                            <p>
                                گزارشات
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">

                            <!-- زیرمنوی نمایشگاه -->
                            <li class="nav-item has-treeview
            {{ request()->routeIs(
                'A_report_of_exhibition_customers',
                'A_report_of_exhibition_visitors',
                'A_report_of_exhibition_visitors_by_city'
            ) ? 'menu-open' : '' }}">

                                <a href="#"
                                   class="nav-link
               {{ request()->routeIs(
                   'A_report_of_exhibition_customers',
                   'A_report_of_exhibition_visitors',
                   'A_report_of_exhibition_visitors_by_city'
               ) ? 'active' : '' }}">
                                    <i class="fa fa-building" aria-hidden="true"></i>
                                    <p>
                                        نمایشگاه
                                        <i class="right fa fa-angle-left"></i>
                                    </p>
                                </a>

                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('A_report_of_exhibition_customers') }}"
                                           class="nav-link {{ request()->routeIs('A_report_of_exhibition_customers') ? 'active' : '' }}">
                                            <p>اطلاعات بازدیدکنندگان </p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ route('A_report_of_exhibition_visitors') }}"
                                           class="nav-link {{ request()->routeIs('A_report_of_exhibition_visitors') ? 'active' : '' }}">
                                            <p>آمار بازدیدکنندگان براساس نمایشگاه</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('A_report_of_exhibition_visitors_by_city') }}"
                                           class="nav-link {{ request()->routeIs('A_report_of_exhibition_visitors_by_city') ? 'active' : '' }}">
                                            <p>آمار بازدیدکنندگان براساس شهر</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- گزارش سایت -->
                            <li class="nav-item">
                                <a href="{{ route('A_report_of_site_customers') }}"
                                   class="nav-link {{ request()->routeIs('A_report_of_site_customers') ? 'active' : '' }}">
                                    <p> مشتریان سایت</p>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa fa-th"></i>
                            <p>
                                لینک ساده
                                <span class="right badge badge-danger">جدید</span>
                            </p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
    </div>
    <!-- /.sidebar -->
</aside>
