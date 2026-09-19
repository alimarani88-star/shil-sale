<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminAccessService
{
    public function currentUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    public function isStaff(?User $user = null): bool
    {
        $user ??= $this->currentUser();

        return $user !== null && $user->isStaff();
    }

    public function isSuperAdmin(?User $user = null): bool
    {
        $user ??= $this->currentUser();
        if (!$this->isStaff($user)) {
            return false;
        }

        return (bool) $user->adminRole()?->is_super_admin;
    }

    public function can(string $permission, ?User $user = null): bool
    {
        $user ??= $this->currentUser();
        if (!$this->isStaff($user)) {
            return false;
        }
        if ($this->isSuperAdmin($user)) {
            return true;
        }
        if ($permission === '') {
            return true;
        }

        return $user->adminPermissions()->contains($permission);
    }

    public function permissionForRoute(?string $routeName): ?string
    {
        if ($routeName === null || $routeName === '') {
            return '__deny__';
        }

        $map = config('admin_access.route_permissions', []);
        if (!array_key_exists($routeName, $map)) {
            return '__deny__';
        }

        $permission = $map[$routeName];

        return $permission === null ? '' : (string) $permission;
    }

    public function allowsCurrentRoute(?User $user = null): bool
    {
        $user ??= $this->currentUser();
        if (!$this->isStaff($user)) {
            return false;
        }
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        $permission = $this->permissionForRoute(request()->route()?->getName());
        if ($permission === '__deny__') {
            return false;
        }

        return $this->can($permission, $user);
    }
}
