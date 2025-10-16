<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'role',
        'assigned_at',
        'unassigned_at',
        'assigned_by',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'unassigned_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the order that owns the assignment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the assigned user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who made the assignment.
     */
    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get formatted role name.
     */
    public function getFormattedRoleAttribute(): string
    {
        $roles = [
            'primary' => 'مسؤول رئيسي',
            'secondary' => 'مسؤول مساعد',
            'designer' => 'مصمم',
            'manufacturer' => 'مصنع',
            'quality_controller' => 'مراقب جودة',
            'shipping_coordinator' => 'منسق شحن',
            'customer_service' => 'خدمة عملاء',
            'sales_representative' => 'مندوب مبيعات',
        ];

        return $roles[$this->role] ?? $this->role;
    }

    /**
     * Get role color for UI.
     */
    public function getRoleColorAttribute(): string
    {
        return match($this->role) {
            'primary' => 'primary',
            'secondary' => 'info',
            'designer' => 'success',
            'manufacturer' => 'warning',
            'quality_controller' => 'danger',
            'shipping_coordinator' => 'dark',
            'customer_service' => 'secondary',
            'sales_representative' => 'light',
            default => 'secondary'
        };
    }

    /**
     * Get role icon for UI.
     */
    public function getRoleIconAttribute(): string
    {
        return match($this->role) {
            'primary' => 'fas fa-user-tie',
            'secondary' => 'fas fa-user-friends',
            'designer' => 'fas fa-drafting-compass',
            'manufacturer' => 'fas fa-industry',
            'quality_controller' => 'fas fa-check-circle',
            'shipping_coordinator' => 'fas fa-shipping-fast',
            'customer_service' => 'fas fa-headset',
            'sales_representative' => 'fas fa-handshake',
            default => 'fas fa-user'
        };
    }

    /**
     * Assign a user to an order.
     */
    public static function assignUser(
        int $orderId,
        int $userId,
        string $role,
        ?string $notes = null,
        ?int $assignedBy = null
    ): self {
        // Deactivate any existing assignment for this role
        self::where('order_id', $orderId)
            ->where('role', $role)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'unassigned_at' => now(),
            ]);

        // Create new assignment
        $assignment = self::create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'role' => $role,
            'assigned_at' => now(),
            'assigned_by' => $assignedBy ?? auth()->id(),
            'notes' => $notes,
            'is_active' => true,
        ]);

        // Log the action
        $user = User::find($userId);
        OrderLog::createLog(
            $orderId,
            'assigned',
            null,
            $user->name . " ({$assignment->formatted_role})",
            "تم تعيين {$user->name} كـ {$assignment->formatted_role}"
        );

        return $assignment;
    }

    /**
     * Unassign a user from an order.
     */
    public function unassign(?string $reason = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $this->update([
            'is_active' => false,
            'unassigned_at' => now(),
            'notes' => $reason ? ($this->notes ? $this->notes . "\n" . $reason : $reason) : $this->notes,
        ]);

        // Log the action
        OrderLog::createLog(
            $this->order_id,
            'unassigned',
            $this->user->name . " ({$this->formatted_role})",
            null,
            "تم إلغاء تعيين {$this->user->name} من منصب {$this->formatted_role}"
        );

        return true;
    }

    /**
     * Get active assignments for an order.
     */
    public static function getOrderAssignments(int $orderId)
    {
        return self::where('order_id', $orderId)
            ->where('is_active', true)
            ->with(['user', 'assignedByUser'])
            ->orderBy('assigned_at')
            ->get();
    }

    /**
     * Get assignments for a user.
     */
    public static function getUserAssignments(int $userId, bool $activeOnly = true)
    {
        $query = self::where('user_id', $userId)
            ->with(['order', 'assignedByUser']);

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('assigned_at', 'desc')->get();
    }

    /**
     * Get primary assignee for an order.
     */
    public static function getPrimaryAssignee(int $orderId): ?self
    {
        return self::where('order_id', $orderId)
            ->where('role', 'primary')
            ->where('is_active', true)
            ->with('user')
            ->first();
    }

    /**
     * Check if user is assigned to order.
     */
    public static function isUserAssigned(int $orderId, int $userId): bool
    {
        return self::where('order_id', $orderId)
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get assignment statistics.
     */
    public static function getAssignmentStats()
    {
        return [
            'total_assignments' => self::where('is_active', true)->count(),
            'assignments_by_role' => self::where('is_active', true)
                ->selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role'),
            'assignments_by_user' => self::where('is_active', true)
                ->with('user')
                ->selectRaw('user_id, COUNT(*) as count')
                ->groupBy('user_id')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->user->name => $item->count];
                }),
        ];
    }
}
