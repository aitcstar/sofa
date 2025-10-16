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
    ];

    protected $casts = [
        'description_ar' => 'array',
        'description_en' => 'array',
    ];

    public function stageStatuses()
    {
        return $this->hasMany(OrderStageStatus::class);
    }
}
