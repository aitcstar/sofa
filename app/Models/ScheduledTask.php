<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ScheduledTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'data',
        'scheduled_at',
        'status',
        'result',
        'attempts',
        'max_attempts',
        'executed_at',
        'next_retry_at'
    ];

    protected $casts = [
        'data' => 'array',
        'scheduled_at' => 'datetime',
        'executed_at' => 'datetime',
        'next_retry_at' => 'datetime'
    ];

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'pending')
                    ->where('scheduled_at', '<=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                    ->where('scheduled_at', '<', now()->subHours(1));
    }

    // Helper Methods
    public function markAsRunning()
    {
        $this->status = 'running';
        $this->save();
    }

    public function markAsCompleted($result = null)
    {
        $this->status = 'completed';
        $this->executed_at = now();
        $this->result = $result;
        $this->save();
    }

    public function markAsFailed($error = null)
    {
        $this->status = 'failed';
        $this->executed_at = now();
        $this->result = $error;
        $this->attempts++;
        
        // جدولة إعادة المحاولة إذا لم نصل للحد الأقصى
        if ($this->attempts < $this->max_attempts) {
            $this->status = 'pending';
            $this->next_retry_at = now()->addMinutes(pow(2, $this->attempts)); // Exponential backoff
        }
        
        $this->save();
    }

    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public function retry()
    {
        $this->status = 'pending';
        $this->scheduled_at = now();
        $this->next_retry_at = null;
        $this->save();
    }

    public function canRetry()
    {
        return $this->attempts < $this->max_attempts && $this->status === 'failed';
    }

    public function isReady()
    {
        return $this->status === 'pending' && $this->scheduled_at <= now();
    }

    public function isOverdue()
    {
        return $this->status === 'pending' && $this->scheduled_at < now()->subHours(1);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'running' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'running' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'failed' => 'فاشل',
            'cancelled' => 'ملغى',
            default => 'غير محدد'
        };
    }

    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'send_reminder' => 'إرسال تذكير',
            'check_overdue_orders' => 'فحص الطلبات المتأخرة',
            'send_invoice_reminder' => 'تذكير بالفاتورة',
            'backup_data' => 'نسخ احتياطي للبيانات',
            'generate_report' => 'إنشاء تقرير',
            'cleanup_logs' => 'تنظيف السجلات',
            'send_notification' => 'إرسال إشعار',
            default => 'مهمة مخصصة'
        };
    }

    public function getDurationAttribute()
    {
        if (!$this->executed_at) {
            return null;
        }
        
        $start = $this->updated_at; // وقت بدء التنفيذ
        $end = $this->executed_at;
        
        return $end->diffInSeconds($start);
    }

    public function getNextRetryInAttribute()
    {
        if (!$this->next_retry_at) {
            return null;
        }
        
        return now()->diffInMinutes($this->next_retry_at, false);
    }
}
