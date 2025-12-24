<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    protected $fillable = [
        'category_id',
        'package_id',
        'name_ar',
        'name_en',
        'summary_ar',
        'summary_en',
        'description_ar',
        'description_en',
        'delivery_date',
        'is_active',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'slug_en',
        'slug_ar',
        'city_ar',
        'city_en'
    ];

    public function category()
    {
        return $this->belongsTo(ExhibitionCategory::class, 'category_id');
    }

    public function packages()
{
    return $this->belongsTo(Package::class, 'package_id');
}


public function primaryImage()
{
    return $this->hasOne(ExhibitionImage::class)->where('is_primary', 1);
}


    public function images()
    {
        return $this->hasMany(ExhibitionImage::class)->orderBy('sort_order');
    }

    public function steps()
    {
        return $this->hasMany(ExhibitionStep::class);
    }

    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getSummaryAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->summary_ar : $this->summary_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    public function package()
{
    return $this->belongsTo(Package::class, 'package_id');
}
}
