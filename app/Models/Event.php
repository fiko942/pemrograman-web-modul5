<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'location',
        'city',
        'start_date',
        'capacity',
        'available_seats',
        'ticket_price',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'is_featured' => 'boolean',
    ];
}
