<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post_type extends Model
{
    protected $table = 'post_type';
    protected $fillable = [
        'post_id',
        'process_id',
        'process_name',
        'type',
        'primary'

    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

}
