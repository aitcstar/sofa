<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlider extends Model
{
    protected $fillable = [
        'title_ar', 'title_en', 'description_ar', 'description_en', 'order', 'is_active', 'image'
    ];

    // الحصول على النص حسب اللغة الحالية
    public function title() {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function description() {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }
}
