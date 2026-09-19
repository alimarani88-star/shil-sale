<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    public const TYPE_STAFF = 'user';
    public const TYPE_CUSTOMER = 'customer';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    protected $table = 'users';

    protected ?Collection $cachedAdminPermissionSlugs = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function favoritePosts() {
        return $this->morphedByMany(Post::class, 'favoritable', 'favorites')->withTimestamps();
    }

    public function favoriteProducts() {
        return $this->morphedByMany(Product::class, 'favoritable', 'favorites')->withTimestamps();
    }
    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id','id');
    }
    public function latestAddress()
    {
        return $this->hasOne(Address::class, 'user_id', 'id')
            ->whereNull('deleted_at')
            ->latest('id');
    }
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }

    public function userRole(): HasOne
    {
        return $this->hasOne(UserRole::class, 'user_id');
    }

    public function isStaff(): bool
    {
        return (string) $this->type === self::TYPE_STAFF;
    }

    public function isCustomer(): bool
    {
        return (string) $this->type === self::TYPE_CUSTOMER;
    }

    public function contactMobile(): ?string
    {
        if (!$this->isCustomer()) {
            return null;
        }

        $mobile = (string) $this->username;

        return preg_match('/^09\d{9}$/', $mobile) === 1 ? $mobile : null;
    }

    public function adminRole(): ?Role
    {
        return $this->userRole?->role;
    }

    public function adminPermissions(): Collection
    {
        if ($this->cachedAdminPermissionSlugs !== null) {
            return $this->cachedAdminPermissionSlugs;
        }

        $role = $this->adminRole();
        if ($role === null) {
            return $this->cachedAdminPermissionSlugs = collect();
        }

        if ($role->isSuperAdmin()) {
            return $this->cachedAdminPermissionSlugs = collect(array_keys(config('admin_access.permissions', [])));
        }

        if (!$role->relationLoaded('permissions')) {
            $role->load('permissions');
        }

        return $this->cachedAdminPermissionSlugs = $role->permissions->pluck('slug');
    }
}
