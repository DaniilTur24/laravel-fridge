<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FridgeItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'quantity', 'weight_grams', 'comment'];

    protected $casts = [
        'quantity'     => 'integer',
        'weight_grams' => 'integer',
    ];
}
