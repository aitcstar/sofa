<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class OrderTimeline extends Model
{
    use HasFactory;

    protected $table = 'order_timeline';

    protected $fillable = [
        'order_id',
        'stage',
        'status',
        'planned_start_date',
        'actual_start_date',
        'planned_end_date',
        'actual_end_date',
        'notes',
        'attachments',
        'progress_percentage',
        'assigned_to',
    ];

    protected $casts = [
        'planned_start_date' => 'datetime',
        'actual_start_date' => 'datetime',
        'planned_end_date' => 'datetime',
        'actual_end_date' => 'datetime',
        'attachments' => 'array',
        'progress_percentage' => 'integer',
    ];

    /**
     * Get the order that owns the timeline.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user assigned to this stage.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get formatted stage name.
     */
    public function getFormattedStageAttribute(): string
    {
        $stages = [
            'design' => 'التصميم',
            'manufacturing' => 'التصنيع',
            'shipping' => 'الشحن',
            'first_payment' => 'الدفعة الأولى',
            'second_payment' => 'الدفعة الثانية',
            'quality_check' => 'فحص الجودة',
            'packaging' => 'التعبئة والتغليف',
            'delivery' => 'التسليم',
        ];

        return $stages[$this->stage] ?? $this->stage;
    }

    /**
     * Get formatted status name.
     */
    public function getFormattedStatusAttribute(): string
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'delayed' => 'متأخر',
            'cancelled' => 'ملغي',
            'on_hold' => 'معلق',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'secondary',
            'in_progress' => 'primary',
            'completed' => 'success',
            'delayed' => 'danger',
            'cancelled' => 'dark',
            'on_hold' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get stage icon for UI.
     */
    public function getStageIconAttribute(): string
    {
        return match($this->stage) {
            'design' => 'fas fa-drafting-compass',
            'manufacturing' => 'fas fa-industry',
            'shipping' => 'fas fa-shipping-fast',
            'first_payment' => 'fas fa-money-bill-wave',
            'second_payment' => 'fas fa-credit-card',
            'quality_check' => 'fas fa-check-circle',
            'packaging' => 'fas fa-box',
            'delivery' => 'fas fa-truck',
            default => 'fas fa-clock'
        };
    }

    /**
     * Check if stage is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->planned_end_date || $this->status === 'completed') {
            return false;
        }

        return Carbon::now()->isAfter($this->planned_end_date);
    }

    /**
     * Get days remaining or overdue.
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->planned_end_date || $this->status === 'completed') {
            return null;
        }

        return Carbon::now()->diffInDays($this->planned_end_date, false);
    }

    /**
     * Get duration in days.
     */
    public function getDurationDaysAttribute(): ?int
    {
        if (!$this->actual_start_date || !$this->actual_end_date) {
            return null;
        }

        return $this->actual_start_date->diffInDays($this->actual_end_date);
    }

    /**
     * Start a timeline stage.
     */
    public function start(?string $notes = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'in_progress',
            'actual_start_date' => now(),
            'notes' => $notes ? ($this->notes ? $this->notes . "\n" . $notes : $notes) : $this->notes,
        ]);

        // Log the action
        OrderLog::createLog(
            $this->order_id,
            'timeline_updated',
            'pending',
            'in_progress',
            "تم بدء مرحلة {$this->formatted_stage}"
        );

        return true;
    }

    /**
     * Complete a timeline stage.
     */
    public function complete(?string $notes = null): bool
    {
        if ($this->status !== 'in_progress') {
            return false;
        }

        $this->update([
            'status' => 'completed',
            'actual_end_date' => now(),
            'progress_percentage' => 100,
            'notes' => $notes ? ($this->notes ? $this->notes . "\n" . $notes : $notes) : $this->notes,
        ]);

        // Log the action
        OrderLog::createLog(
            $this->order_id,
            'timeline_updated',
            'in_progress',
            'completed',
            "تم إكمال مرحلة {$this->formatted_stage}"
        );

        return true;
    }

    /**
     * Update progress percentage.
     */
    public function updateProgress(int $percentage, ?string $notes = null): bool
    {
        if ($percentage < 0 || $percentage > 100) {
            return false;
        }

        $oldProgress = $this->progress_percentage;
        
        $this->update([
            'progress_percentage' => $percentage,
            'notes' => $notes ? ($this->notes ? $this->notes . "\n" . $notes : $notes) : $this->notes,
        ]);

        // Log the action
        OrderLog::createLog(
            $this->order_id,
            'progress_updated',
            $oldProgress . '%',
            $percentage . '%',
            "تم تحديث تقدم مرحلة {$this->formatted_stage} إلى {$percentage}%"
        );

        return true;
    }

    /**
     * Get timeline for a specific order.
     */
    public static function getOrderTimeline(int $orderId)
    {
        return self::where('order_id', $orderId)
            ->with('assignedUser')
            ->orderBy('id')
            ->get();
    }

    /**
     * Create default timeline for an order.
     */
    public static function createDefaultTimeline(int $orderId): void
    {
        $stages = [
            'design' => ['duration' => 7, 'order' => 1],
            'manufacturing' => ['duration' => 14, 'order' => 2],
            'quality_check' => ['duration' => 2, 'order' => 3],
            'packaging' => ['duration' => 1, 'order' => 4],
            'shipping' => ['duration' => 3, 'order' => 5],
            'delivery' => ['duration' => 1, 'order' => 6],
        ];

        $startDate = now();

        foreach ($stages as $stage => $config) {
            $plannedStart = $startDate->copy()->addDays(
                array_sum(array_slice(array_column($stages, 'duration'), 0, $config['order'] - 1))
            );
            $plannedEnd = $plannedStart->copy()->addDays($config['duration']);

            self::create([
                'order_id' => $orderId,
                'stage' => $stage,
                'status' => 'pending',
                'planned_start_date' => $plannedStart,
                'planned_end_date' => $plannedEnd,
                'progress_percentage' => 0,
            ]);
        }
    }

    /**
     * Get overdue stages.
     */
    public static function getOverdueStages()
    {
        return self::where('status', '!=', 'completed')
            ->where('planned_end_date', '<', now())
            ->with(['order', 'assignedUser'])
            ->get();
    }
}
