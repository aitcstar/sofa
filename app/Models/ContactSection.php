<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSection extends Model
{
    protected $fillable = [
        'title_ar','title_en','desc_ar','desc_en',
        'main_showroom_ar','main_showroom_en',
        'work_hours_ar','work_hours_en',
        'cta_heading_ar','cta_heading_en',
        'cta_text_ar','cta_text_en',
        'city_ar','city_en',
        'address_ar','address_en',
    ];
}

