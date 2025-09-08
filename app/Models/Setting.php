<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'email',
        'worktime',
        'phone',
        'whatsapp',
        'address',
        'snapchat',
        'tiktok',
        'instagram',
        'linkedin',
        'youtube',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];
}
