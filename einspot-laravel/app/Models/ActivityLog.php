<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'loggable_id',
        'loggable_type',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'json',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was acted upon.
     */
    public function loggable()
    {
        return $this->morphTo();
    }

    /**
     * Helper to log an activity.
     *
     * @param string $action Example: 'created_product'
     * @param Model $loggable The model instance being logged.
     * @param string|null $description A human-readable description.
     * @param array|null $properties Additional data (e.g., changed attributes).
     * @param User|null $user The user performing the action (defaults to authenticated user).
     * @return void
     */
    public static function record(string $action, Model $loggable, ?string $description = null, ?array $properties = null, ?User $user = null)
    {
        $user = $user ?? auth()->user();

        static::create([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'loggable_id' => $loggable->getKey(),
            'loggable_type' => $loggable->getMorphClass(), // getMorphClass() handles model class name
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
