<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                 $tag->slug = Str::slug($tag->name);
            } elseif ($tag->isDirty('name') && !empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * The products that belong to the tag.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_tag');
    }

    /**
     * The blog posts that belong to the tag.
     */
    public function blogPosts()
    {
        return $this->belongsToMany(Blog::class, 'blog_post_tag');
    }
}
