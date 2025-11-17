<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStageStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_stage_id',
        'status',
        'completed_at',
    ];
    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function stage()
    {
        return $this->belongsTo(OrderStage::class, 'order_stage_id');
    }

    protected static function booted()
{
    static::created(function($stageStatus) {
        $stage = $stageStatus->stage;

        // لو المرحلة نفسها فرعية
        if ($stage->parent_id) {

            $parent = $stage->parent;

            // كل المراحل الفرعية الخاصة بالأب
            $childStages = $parent->children()->pluck('id');

            // حالات المراحل الفرعية للطلب
            $completedChildren = OrderStageStatus::where('order_id', $stageStatus->order_id)
                ->whereIn('order_stage_id', $childStages)
                ->where('status', 'completed')
                ->count();

            if ($completedChildren == $childStages->count()) {
                // اكتمال المرحلة الرئيسية
                OrderStageStatus::updateOrCreate(
                    [
                        'order_id' => $stageStatus->order_id,
                        'order_stage_id' => $parent->id,
                    ],
                    [
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]
                );
            }
        }
    });
}

}
