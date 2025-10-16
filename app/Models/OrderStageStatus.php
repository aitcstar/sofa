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
}
