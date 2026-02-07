<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    Protected $fillable = [
        "module"
        ,"process_id"
        ,"user_id"
        ,"user_name"
        ,"approved_user_id"
        ,"status"
        ,"content"
        ,"reply"
    ];


    public function post()
    {
        return $this->belongsTo(Post::class, 'process_id')
            ->where('module', 'post');
    }
}

