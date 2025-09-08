<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'category',
        'description_ar',
        'description_en',
        'image_path',
    ];

    // العلاقات
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'unit_designs')
                    ->withPivot('is_default')
                    ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
