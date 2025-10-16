<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'channel',
        'subject',
        'body',
        'variables',
        'is_active',
        'language',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    // Helper Methods
    public function renderContent($variables = [])
    {
        $content = $this->content;
        
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    public function renderSubject($variables = [])
    {
        $subject = $this->subject;
        
        foreach ($variables as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
        }
        
        return $subject;
    }

    public function getAvailableVariables()
    {
        return $this->variables ?? [];
    }

    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    public function getChannelTextAttribute()
    {
        return match($this->channel) {
            'email' => 'البريد الإلكتروني',
            'sms' => 'رسالة نصية',
            'whatsapp' => 'واتساب',
            'database' => 'النظام',
            default => 'غير محدد'
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
            default => 'غير محدد'
        };
    }
}
