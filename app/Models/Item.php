<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'category_id'
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'price' => 'float',
        'quantity' => 'integer',
        'category_id' => 'integer'
    ];
}
