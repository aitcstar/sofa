<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderTimelineSection extends Model
{
    protected $fillable = ['title_en','title_ar','desc_en','desc_ar'];

    public function items()
    {
        return $this->hasMany(OrderTimelineItem::class, 'section_id');
    }
}
