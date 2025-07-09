<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipping_address',
        'billing_address',
        'sub_total',
        'vat_amount',
        'shipping_cost',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'shipping_address' => 'json', // Assuming structured address might be stored as JSON
        'billing_address' => 'json', // Same as above
        'sub_total' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
