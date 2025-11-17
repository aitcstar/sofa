<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'order_number',
        'parent_id',
    ];

    protected $casts = [
        'description_ar' => 'array',
        'description_en' => 'array',
    ];

    // علاقة المرحلة بمراحلها الفرعية
    public function children()
    {
        return $this->hasMany(OrderStage::class, 'parent_id');
    }

    // علاقة المرحلة بالمرحلة الأب
    public function parent()
    {
        return $this->belongsTo(OrderStage::class, 'parent_id');
    }

    // حالة المرحلة للطلب
    public function stageStatuses()
    {
        return $this->hasMany(OrderStageStatus::class);
    }
}
