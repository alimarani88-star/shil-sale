<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Services\AdminAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffAccessController extends Controller
{
    public function index(): View
    {
        $staff = User::query()
            ->where('type', User::TYPE_STAFF)
            ->with(['userRole.role'])
            ->orderBy('id')
            ->get();

        $roles = Role::query()->orderBy('id')->get();

        return view('Admin.Access.A_staff_access', compact('staff', 'roles'));
    }

    public function update(Request $request, AdminAccessService $access): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $result = DB::transaction(function () use ($validated, $request, $access) {
            $staff = User::query()
                ->where('id', $validated['user_id'])
                ->where('type', User::TYPE_STAFF)
                ->lockForUpdate()
                ->first();

            if ($staff === null) {
                return [
                    'ok' => false,
                    'message' => 'فقط کارکنان پنل قابل ویرایش هستند.',
                ];
            }

            $role = Role::query()->lockForUpdate()->find($validated['role_id']);
            if ($role === null) {
                return [
                    'ok' => false,
                    'message' => 'نقش انتخاب‌شده معتبر نیست.',
                ];
            }

            if ($role->isSuperAdmin() && !$access->isSuperAdmin($request->user())) {
                return [
                    'ok' => false,
                    'message' => 'فقط مدیرکل می‌تواند نقش مدیرکل را اختصاص دهد.',
                ];
            }

            $currentRole = $staff->adminRole();
            if ($currentRole?->isSuperAdmin() && !$role->isSuperAdmin()) {
                $superCount = DB::table('user_role')
                    ->join('roles', 'roles.id', '=', 'user_role.role_id')
                    ->where('roles.is_super_admin', true)
                    ->lockForUpdate()
                    ->count();

                if ($superCount <= 1) {
                    return [
                        'ok' => false,
                        'message' => 'نمی‌توان آخرین مدیرکل را از این نقش خارج کرد.',
                    ];
                }
            }

            UserRole::query()->updateOrCreate(
                ['user_id' => $staff->id],
                ['role_id' => $role->id]
            );

            _log(
                $staff->id,
                'update',
                'user_role',
                'staff_access',
                'تغییر نقش به ' . $role->name
            );

            return [
                'ok' => true,
                'message' => 'نقش کاربر به‌روزرسانی شد.',
            ];
        });

        return redirect()
            ->route('A_staff_access')
            ->with($result['ok'] ? 'swal-success' : 'swal-error', $result['message']);
    }
}
