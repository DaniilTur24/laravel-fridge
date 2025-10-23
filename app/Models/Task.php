<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Разрешаем массовое заполнение этого поля
    protected $fillable = ['title', 'is_done'];
}

