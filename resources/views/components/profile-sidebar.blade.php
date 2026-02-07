@php
    use Illuminate\Support\Facades\Route;

    $current = Route::currentRouteName();
@endphp

<div class="profile-page-aside col-xl-3 col-lg-4 col-md-6 center-section order-1">
    <div class="profile-box">
        <div class="profile-box-header custom-primary">
            <div class="profile-box-avatar">
                <img src="{{ asset('assets/img/svg/user.svg') }}" alt="">
            </div>
            <button data-toggle="modal" data-target="#myModal" class="profile-box-btn-edit">
                <i class="fa fa-pencil"></i>
            </button>
        </div>
        <div class="profile-box-username">{{ $user->name ?? 'کاربر مهمان' }}</div>
        <div class="profile-box-tabs">
            <a href="{{ route('reset_password') }}" class="profile-box-tab profile-box-tab-access">
                <i class="now-ui-icons ui-1_lock-circle-open"></i>
                تغییر رمز
            </a>
            <a href="#" class="profile-box-tab profile-box-tab--sign-out btn-logout">
                <i class="now-ui-icons media-1_button-power"></i>
                خروج از حساب
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
    </div>

    <!-- منوی ریسپانسیو (موبایل) -->
    <div class="responsive-profile-menu show-md">
        <div class="btn-group">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <i class="fad fa-bars"></i>
                حساب کاربری شما
            </button>
            <div class="dropdown-menu dropdown-menu-right text-right">

                <a href="{{ route('profile_personal_info') }}"
                   class="dropdown-item {{ $current === 'profile_personal_info' ? 'active' : '' }}">
                    <i class="now-ui-icons users_single-02"></i> پروفایل
                </a>

                <a href="{{ route('profile_orders') }}"
                   class="dropdown-item {{ $current === 'profile_orders' ? 'active' : '' }}">
                    <i class="now-ui-icons shopping_basket"></i> همه سفارش‌ها
                </a>

                <a href="{{ route('profile_addresses') }}"
                   class="dropdown-item {{ $current === 'profile_addresses' ? 'active' : '' }}">
                    <i class="now-ui-icons location_pin"></i> آدرس‌ها
                </a>

                <a href="{{ route('profile_orders_return') }}"
                   class="dropdown-item {{ $current === 'profile_orders_return' ? 'active' : '' }}">
                    <i class="now-ui-icons files_single-copy-04"></i> درخواست مرجوعی
                </a>

                <a href="{{ route('profile_favorites') }}"
                   class="dropdown-item {{ $current === 'profile_favorites' ? 'active' : '' }}">
                    <i class="now-ui-icons ui-2_favourite-28"></i> لیست علاقمندی‌ها
                </a>

            </div>
        </div>
    </div>

    <!-- منوی دسکتاپ -->
    <div class="profile-menu hidden-md">
        <div class="profile-menu-header">حساب کاربری شما</div>
        <ul class="profile-menu-items">
            <li>
                <a href="{{ route('profile_personal_info') }}"
                   class="{{ $current === 'profile_personal_info' ? 'active' : '' }}">
                    <i class="now-ui-icons users_single-02"></i> پروفایل
                </a>
            </li>

            <li>
                <a href="{{ route('profile_orders') }}"
                   class="{{ $current === 'profile_orders' ? 'active' : '' }}">
                    <i class="now-ui-icons shopping_basket"></i> همه سفارش‌ها
                </a>
            </li>

            <li>
                <a href="{{ route('profile_addresses') }}"
                   class="{{ $current === 'profile_addresses' ? 'active' : '' }}">
                    <i class="now-ui-icons location_pin"></i> آدرس‌ها
                </a>
            </li>

            <li>
                <a href="{{ route('profile_orders_return') }}"
                   class="{{ $current === 'profile_orders_return' ? 'active' : '' }}">
                    <i class="now-ui-icons files_single-copy-04"></i> درخواست مرجوعی
                </a>
            </li>

            <li>
                <a href="{{ route('profile_favorites') }}"
                   class="{{ $current === 'profile_favorites' ? 'active' : '' }}">
                    <i class="now-ui-icons ui-2_favourite-28"></i> لیست علاقمندی‌ها
                </a>
            </li>
        </ul>
    </div>
</div>

@push('scripts')
    <script>

        $(document).on('click', '.btn-logout', function (e) {
            e.preventDefault();

            Swal.fire({
                text: "می‌خوای از حساب کاربریت خارج بشی؟",
                showCancelButton: true,
                confirmButtonText: "بله، خارج شو",
                cancelButtonText: "خیر",
                reverseButtons: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#777",
                customClass: {
                    popup: 'small-swal',
                    title: 'small-swal-title',
                    confirmButton: 'small-swal-confirm',
                    cancelButton: 'small-swal-cancel'
                }
            }).then((result) => {
                if (result.value) {
                    $('#logout-form').submit();
                }
            });
        });
    </script>
@endpush

