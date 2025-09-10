<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    protected $fillable = [
        'icon', 'title_en', 'title_ar', 'desc_en', 'desc_ar', 'order'
    ];

    // طريقة الوصول للعناصر حسب اللغة
    public function getTitleAttribute() {
        return app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getDescAttribute() {
        return app()->getLocale() == 'ar' ? $this->desc_ar : $this->desc_en;
    }
}
