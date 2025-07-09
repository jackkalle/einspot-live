<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'service_of_interest',
        'project_description',
        'estimated_budget',
        'timeline',
        'product_service_name',
        'status',
    ];
}
