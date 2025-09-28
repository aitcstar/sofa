<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExhibitionCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name_ar', 'name_en', 'slug_ar', 'slug_en'];

    public function exhibitions()
    {
        return $this->hasMany(Exhibition::class);
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
