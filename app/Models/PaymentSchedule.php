<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'due_date',
        'status',
    ];

    // العلاقة مع الطلبات
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
