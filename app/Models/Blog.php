<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title_ar', 'title_en',
        'slug_ar', 'slug_en',
        'excerpt_ar', 'excerpt_en',
        'content_ar', 'content_en',
        'image',
        'author_ar', 'author_en',
        'category_id', // مفتاح أجنبي يربط مع blog_categories
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',

    ];

    /**
     * العلاقة مع التصنيفات
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'blog_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id');
    }


    /**
     * لوكالايز للعنوان
     */
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    /**
     * لوكالايز للـ slug
     */
    public function getSlugAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->slug_ar : $this->slug_en;
    }

    /**
     * لوكالايز للملخص
     */
    public function getExcerptAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->excerpt_ar : $this->excerpt_en;
    }

    /**
     * لوكالايز للمحتوى
     */
    public function getContentAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->content_ar : $this->content_en;
    }

    /**
     * لوكالايز للمؤلف
     */
    public function getAuthorAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->author_ar : $this->author_en;
    }

    /**
     * لوكالايز للقسم (عن طريق العلاقة)
     */
    public function getCategoryNameAttribute()
    {
        if (!$this->category) {
            return null;
        }
        return app()->getLocale() === 'ar' ? $this->category->name_ar : $this->category->name_en;
    }
}
