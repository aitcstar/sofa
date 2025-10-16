<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'activity_type',
        'subject',
        'description',
        'status',
        'scheduled_at',
        'completed_at',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    // العلاقات
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeScheduledToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                    ->where('scheduled_at', '<', now());
    }

    // Accessors
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'call' => 'مكالمة',
            'email' => 'بريد إلكتروني',
            'meeting' => 'اجتماع',
            'note' => 'ملاحظة',
            'task' => 'مهمة',
            'quote_sent' => 'إرسال عرض سعر',
            'follow_up' => 'متابعة',
            'status_changed' => 'تغيير الحالة',
            'assigned' => 'تعيين',
            'converted_to_customer' => 'تحويل إلى عميل',
            'converted_to_order' => 'تحويل إلى طلب',
            default => $this->type
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'call' => 'phone',
            'email' => 'mail',
            'meeting' => 'calendar',
            'note' => 'file-text',
            'task' => 'check-square',
            'quote_sent' => 'file-text',
            'follow_up' => 'clock',
            'status_changed' => 'refresh-cw',
            'assigned' => 'user',
            'converted_to_customer' => 'user-plus',
            'converted_to_order' => 'shopping-cart',
            default => 'activity'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'معلق',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && $this->scheduled_at && $this->scheduled_at->isPast();
    }

    public function getDurationAttribute()
    {
        if ($this->metadata && isset($this->metadata['duration'])) {
            return $this->metadata['duration'];
        }

        if ($this->completed_at && $this->scheduled_at) {
            return $this->scheduled_at->diffInMinutes($this->completed_at);
        }

        return null;
    }

    // Methods
    public function markAsCompleted($completedAt = null)
    {
        $this->status = 'completed';
        $this->completed_at = $completedAt ?: now();
        $this->save();

        // تحديث آخر تواصل في العميل المحتمل
        $this->lead->updateLastContact();
    }

    public function markAsCancelled()
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public function reschedule($newDateTime)
    {
        $this->scheduled_at = $newDateTime;
        $this->status = 'pending';
        $this->save();
    }

    public function addMetadata($key, $value)
    {
        $metadata = $this->metadata ?: [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        $this->save();
    }

    public function getMetadata($key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    // Static Methods
    public static function getTypeOptions()
    {
        return [
            'call' => 'مكالمة',
            'email' => 'بريد إلكتروني',
            'meeting' => 'اجتماع',
            'note' => 'ملاحظة',
            'task' => 'مهمة',
            'quote_sent' => 'إرسال عرض سعر',
            'follow_up' => 'متابعة'
        ];
    }

    public static function createCall($leadId, $userId, $subject, $description = null, $duration = null)
    {
        $metadata = [];
        if ($duration) {
            $metadata['duration'] = $duration;
        }

        return static::create([
            'lead_id' => $leadId,
            'user_id' => $userId,
            'type' => 'call',
            'subject' => $subject,
            'description' => $description,
            'status' => 'completed',
            'completed_at' => now(),
            'metadata' => $metadata
        ]);
    }

    public static function createEmail($leadId, $userId, $subject, $description = null)
    {
        return static::create([
            'lead_id' => $leadId,
            'user_id' => $userId,
            'type' => 'email',
            'subject' => $subject,
            'description' => $description,
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public static function createMeeting($leadId, $userId, $subject, $scheduledAt, $description = null)
    {
        return static::create([
            'lead_id' => $leadId,
            'user_id' => $userId,
            'type' => 'meeting',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
            'scheduled_at' => $scheduledAt
        ]);
    }

    public static function createNote($leadId, $userId, $subject, $description = null)
    {
        return static::create([
            'lead_id' => $leadId,
            'user_id' => $userId,
            'type' => 'note',
            'subject' => $subject,
            'description' => $description,
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public static function createTask($leadId, $userId, $subject, $scheduledAt, $description = null)
    {
        return static::create([
            'lead_id' => $leadId,
            'user_id' => $userId,
            'type' => 'task',
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
            'scheduled_at' => $scheduledAt
        ]);
    }

    public static function createFollowUp($leadId, $userId, $scheduledAt, $description = null)
    {
        return static::create([
            'lead_id' => $leadId,
            'user_id' => $userId,
            'type' => 'follow_up',
            'subject' => 'متابعة مجدولة',
            'description' => $description,
            'status' => 'pending',
            'scheduled_at' => $scheduledAt
        ]);
    }
}
