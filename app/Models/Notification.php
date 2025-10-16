<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'title',
        'message',
        'data',
        'channel',
        'read_at',
        'sent_at',
        'status',
        'error_message',
        'priority',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'priority' => 'integer',
    ];

    // العلاقات
    public function notifiable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeUnsent($query)
    {
        return $query->where('sent', false);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper Methods
    public function markAsRead()
    {
        $this->read_at = now();
        $this->save();
    }

    public function markAsSent()
    {
        $this->sent = true;
        $this->sent_at = now();
        $this->save();
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'secondary',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'info'
        };
    }

    public function getPriorityTextAttribute()
    {
        return match($this->priority) {
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة',
            default => 'متوسطة'
        };
    }

    public function getChannelTextAttribute()
    {
        return match($this->channel) {
            'database' => 'النظام',
            'email' => 'البريد الإلكتروني',
            'sms' => 'رسالة نصية',
            'whatsapp' => 'واتساب',
            default => 'النظام'
        };
    }

    public function getChannelIconAttribute()
    {
        return match($this->channel) {
            'database' => 'fas fa-bell',
            'email' => 'fas fa-envelope',
            'sms' => 'fas fa-sms',
            'whatsapp' => 'fab fa-whatsapp',
            default => 'fas fa-bell'
        };
    }

    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'order_created' => 'طلب جديد',
            'order_updated' => 'تحديث طلب',
            'order_status_changed' => 'تغيير حالة طلب',
            'payment_received' => 'استلام دفعة',
            'order_overdue' => 'طلب متأخر',
            'order_reminder' => 'تذكير بطلب',
            'invoice_created' => 'فاتورة جديدة',
            'invoice_overdue' => 'فاتورة متأخرة',
            'employee_assigned' => 'تعيين موظف',
            'system_alert' => 'تنبيه النظام',
            default => 'إشعار'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'order_created' => 'fas fa-plus-circle',
            'order_updated' => 'fas fa-edit',
            'order_status_changed' => 'fas fa-exchange-alt',
            'payment_received' => 'fas fa-money-bill-wave',
            'order_overdue' => 'fas fa-exclamation-triangle',
            'order_reminder' => 'fas fa-clock',
            'invoice_created' => 'fas fa-file-invoice',
            'invoice_overdue' => 'fas fa-exclamation-circle',
            'employee_assigned' => 'fas fa-user-tag',
            'system_alert' => 'fas fa-bell',
            default => 'fas fa-info-circle'
        };
    }

    public function isRead()
    {
        return !is_null($this->read_at);
    }

    public function isSent()
    {
        return $this->sent;
    }

    public function getTitle()
    {
        return $this->data['title'] ?? $this->type_text;
    }

    public function getMessage()
    {
        return $this->data['message'] ?? '';
    }

    public function getUrl()
    {
        return $this->data['url'] ?? null;
    }

    public function getActionText()
    {
        return $this->data['action_text'] ?? 'عرض';
    }
}
