<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // For Str::slug

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type', // 'product' or 'blog'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            } elseif ($category->isDirty('name') && !empty($category->slug)) {
                // If name changes, update slug only if slug was auto-generated or user wants to update it
                // For simplicity, let's assume if name changes, slug should too, if not manually set.
                // More complex logic might be needed if slugs must be permanent once set.
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Define relationships later
    // public function products()
    // {
    //     return $this->hasMany(Product::class)->where('type', 'product');
    // }

    // public function blogPosts()
    // {
    //     return $this->hasMany(Blog::class)->where('type', 'blog');
    // }
}
