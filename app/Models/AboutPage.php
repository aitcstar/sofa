<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'title_ar',
        'title_en',
        'text_ar',
        'text_en',
        'items_ar',   // نخزنها كـ JSON
        'items_en',
        'image',
    ];

    protected $casts = [
        'items_ar' => 'array',
        'items_en' => 'array',
    ];
}
