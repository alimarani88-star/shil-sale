@extends('Admin.Layout.master')

@section('head-tag')
    <title>دسترسی کارکنان</title>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item font-size-12"><a href="{{ route('A_home') }}">خانه</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">دسترسی کارکنان</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="main-body-container-header">
                        <h4>دسترسی کارکنان</h4>
                        <p>فقط کاربران از نوع کارکنان پنل در این فهرست هستند. مشتریان فروشگاه اینجا نمایش داده نمی‌شوند.</p>
                    </section>

                    <section class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>نام</th>
                                <th>نام کاربری</th>
                                <th>نقش فعلی</th>
                                <th>تغییر نقش</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($staff as $user)
                                <tr>
                                    <td class="text-center" style="width: 60px">{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->adminRole()?->name ?? 'بدون نقش' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('A_s_staff_access') }}" class="d-flex align-items-center">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <select name="role_id" class="form-control form-control-sm ml-2" required>
                                                @foreach($roles as $role)
                                                    @if(!$role->is_super_admin || admin_is_super_admin())
                                                    <option value="{{ $role->id }}" @selected($user->adminRole()?->id === $role->id)>
                                                        {{ $role->name }}
                                                    </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-info btn-sm">ذخیره</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">کارمندی یافت نشد.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </section>
                </section>
            </div>
        </section>
    </div>
@endsection
