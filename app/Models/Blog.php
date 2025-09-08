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
        'category_ar', 'category_en',
        'author_ar', 'author_en'
    ];

    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getSlugAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->slug_ar : $this->slug_en;
    }

    public function getExcerptAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->excerpt_ar : $this->excerpt_en;
    }

    public function getContentAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->content_ar : $this->content_en;
    }

    public function getCategoryAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->category_ar : $this->category_en;
    }

    public function getAuthorAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->author_ar : $this->author_en;
    }
}
