<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'number_of_pieces',
        'available_colors',
        'price',
        'description_ar',
        'description_en',
        'image',
        'period_ar',
        'period_en',
        'service_includes_ar',
        'service_includes_en',
        'payment_plan_ar',
        'payment_plan_en',
        'decoration_ar',
        'decoration_en',
        'is_active',
        'sort_order',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'slug_en',
        'slug_ar',
        'show_in_home'
    ];

    protected $casts = [
        'available_colors' => 'array',
        'is_active' => 'boolean',
    ];

    public function units()
{
    return $this->hasMany(Unit::class);
}

    public function exhibitions()
    {
        return $this->hasMany(Exhibition::class, 'package_id');
    }
    public function images()
    {
        return $this->hasMany(PackageImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(PackageImage::class)->where('is_primary', true);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

     // Accessors
     public function getNameAttribute()
     {
         return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
     }

     public function getDescriptionAttribute()
     {
         return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
     }

     // Mutators


     // Scopes
     public function scopeActive($query)
     {
         return $query->where('is_active', true);
     }

     public function scopeOrdered($query)
     {
         return $query->orderBy('sort_order')->orderBy('name_ar');
     }

public function surveyAnswers()
{
    return $this->hasMany(SurveyQuestion::class, 'package_id');
}

public function packageUnitItems()
{
    return $this->hasMany(PackageUnitItem::class)->with(['unit.images', 'item']);
}


}
