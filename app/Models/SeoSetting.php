<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = [
        'page',
        'meta_title_ar',
        'meta_title_en',
        'meta_description_ar',
        'meta_description_en',
        'slug_ar',
        'slug_en',
        'canonical_ar',
        'canonical_en',
        'index_status',
    ];
}
