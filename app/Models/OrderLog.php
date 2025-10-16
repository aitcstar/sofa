<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'action',
        'old_data',
        'new_data',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order that owns the log.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user that created the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new order log entry.
     */
    public static function createLog(
        int $orderId,
        string $action,
        ?string $oldValue = null,
        ?string $newValue = null,
        ?string $description = null,
        ?array $metadata = null,
        ?int $userId = null
    ): self {
        return self::create([
            'order_id' => $orderId,
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'old_data' => $oldValue,
            'new_data' => $newValue,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get formatted action name.
     */
    public function getFormattedActionAttribute(): string
    {
        $actions = [
            'created' => 'تم إنشاء الطلب',
            'status_changed' => 'تم تغيير حالة الطلب',
            'assigned' => 'تم تعيين موظف',
            'unassigned' => 'تم إلغاء تعيين موظف',
            'note_added' => 'تم إضافة ملاحظة',
            'payment_received' => 'تم استلام دفعة',
            'timeline_updated' => 'تم تحديث الجدول الزمني',
            'priority_changed' => 'تم تغيير الأولوية',
            'customer_contacted' => 'تم التواصل مع العميل',
            'file_uploaded' => 'تم رفع ملف',
            'progress_updated' => 'تم تحديث التقدم',
            'updated' => 'تم تحديث الطلب',
            'cancelled' => 'تم إلغاء الطلب',
            'deleted' => 'تم حذف الطلب',
        ];

        return $actions[$this->action] ?? $this->action;
    }

    /**
     * Get action color for UI.
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'status_changed' => 'warning',
            'assigned' => 'primary',
            'payment_received' => 'success',
            'cancelled' => 'danger',
            'deleted' => 'danger',
            'priority_changed' => 'info',
            'timeline_updated' => 'primary',
            'progress_updated' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get action icon for UI.
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'created' => 'fas fa-plus-circle',
            'updated' => 'fas fa-edit',
            'status_changed' => 'fas fa-exchange-alt',
            'assigned' => 'fas fa-user-tag',
            'payment_received' => 'fas fa-money-bill-wave',
            'cancelled' => 'fas fa-times-circle',
            'deleted' => 'fas fa-trash',
            'priority_changed' => 'fas fa-exclamation-triangle',
            'timeline_updated' => 'fas fa-clock',
            'progress_updated' => 'fas fa-chart-line',
            'note_added' => 'fas fa-sticky-note',
            'customer_contacted' => 'fas fa-phone',
            'file_uploaded' => 'fas fa-file-upload',
            default => 'fas fa-info-circle'
        };
    }

    /**
     * Get logs for a specific order.
     */
    public static function getOrderLogs(int $orderId)
    {
        return self::where('order_id', $orderId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent activity logs.
     */
    public static function getRecentActivity(int $limit = 50)
    {
        return self::with(['order', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
