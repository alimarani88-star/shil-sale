<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'parent_id',
        'app_id',
        'order',
        'app_group_type',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_type', 'process_id', 'post_id')
            ->wherePivot('type', 'category');
    }
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');
    }
    public function childrenLvl1()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }


    /**
     * Find parents of a category
     * for example using in single post show for client
     */
    public function getParents()
    {
        $parents = [];
        $parentId = $this->parent_id;
        $stop = 6 ;
        while ($parentId && $stop>0) {
            $parents[] = $parentId;
            $parent_cat = Category::find($parentId);
            $parentId = $parent_cat->parent_id;
            $stop--;
        }
        return array_reverse($parents);
    }

    public function image()
    {
        return $this->hasOne(Image::class, 'imageable_id')
            ->where('primary', 1)->where('imageable_type', Category::class);
    }

    public static function getTree($parent = null, $depth = 0)
    {
        $maxDepth = 5;
        if ($depth >= $maxDepth) {
            return [];
        }
        $categories = self::where('parent_id', $parent)
            ->where('type', 'post')
            ->orderBy('name')
            ->get();

        $result = [];
        foreach ($categories as $category) {
            $item = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'image' => $category->image,
                'parent_id' => $category->parent_id,
                'depth' => $depth,
                'has_children' => $category->children()->exists(),
            ];
            if ($item['has_children'] && $depth < 4) {
                $item['children'] = self::getTree($category->id, $depth + 1);
            } else {
                $item['children'] = [];
            }

            $result[] = $item;
        }

        return $result;
    }
    public static function getTreeProduct($parent = 0, $depth = 0)
    {
        $maxDepth = 5;
        if ($depth >= $maxDepth) {
            return [];
        }
        $categories = self::where('parent_id', $parent)
            ->where('type', 'product')
            ->orderBy('order')
            ->get();
        $result = [];
        foreach ($categories as $category) {
            $item = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'image' => $category->image ? $category->image['id'] : null,
                'parent_id' => $category->parent_id,
                'depth' => $depth,
                'has_children' => $category->children()->exists(),
            ];
            if ($item['has_children'] && $depth < 4) {
                $item['children'] = self::getTreeProduct($category->id, $depth + 1);
            } else {
                $item['children'] = [];
            }

            $result[] = $item;
        }

        return $result;
    }




}
