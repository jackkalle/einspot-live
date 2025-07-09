<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs'; // Explicitly define table name

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'user_id', // Author
        'category_id', // Blog category
        'image_url',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title') && empty($blog->slug)) {
                 $blog->slug = Str::slug($blog->title);
            } elseif ($blog->isDirty('title') && !empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });
    }

    /**
     * Get the author of the blog post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the blog post.
     */
    public function category()
    {
        // Assuming 'type' column in categories table is 'blog' for these
        return $this->belongsTo(Category::class);
    }

    /**
     * The tags that belong to the blog post.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_post_tag');
    }
}
