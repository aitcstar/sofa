<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderTimelineItem extends Model
{
    protected $fillable = ['section_id','title_en','title_ar','desc_en','desc_ar','color'];

    public function section()
    {
        return $this->belongsTo(OrderTimelineSection::class, 'section_id');
    }
}
