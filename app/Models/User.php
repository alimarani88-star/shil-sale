<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    protected $table = 'users';

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
}
