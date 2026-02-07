<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'summary',
        'content',
        'main_image',
        'slug',
        'published_at',
        'expired_at',
        'author_id',
        'author_name',
        'editor_id',
        'editor_name',
        'status',
        'type',
        'meta_keywords',
        'meta_description',
        'allow_comments',
        'reading_time',
        'alt_main_image',
        'view',
        'custom_fields'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expired_at' => 'datetime',
        'allow_comments' => 'boolean',
        'reading_time' => 'integer'
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class, 'process_id' , 'id')
            ->where('module', 'post');
    }

    public function categories()
    {
        return $this->hasMany(Post_type::class, 'post_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_type', 'post_id', 'process_id')
            ->wherePivot('type', 'tag');
    }
    public function main_image()
    {
        return $this->hasOne(Image::class, 'imageable_id')
            ->where('primary', 1)->where('imageable_type', Post::class);
    }


}
