<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'client',
        'location',
        'duration',
        'status',
        'type',
        'description',
        'image_url',
        'images',
        'brands_used',
        'technologies',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'images' => 'array',
        'brands_used' => 'array',
        'technologies' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && empty($project->slug)) {
                 $project->slug = Str::slug($project->title);
            } elseif ($project->isDirty('title') && !empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }
}
