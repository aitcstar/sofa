<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeAboutSection extends Model
{
    protected $fillable = [
        'image', 'sub_title_en', 'sub_title_ar',
        'title_en', 'title_ar', 'desc_en', 'desc_ar',
        'button_text_en', 'button_text_ar', 'button_link'
    ];

    public function icons()
    {
        return $this->hasMany(HomeAboutIcon::class, 'home_about_section_id')->orderBy('order');
    }

}
