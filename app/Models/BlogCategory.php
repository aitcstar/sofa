<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = [
        'name_ar', 'name_en', 'slug_ar', 'slug_en'
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getSlugAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->slug_ar : $this->slug_en;
    }
}
