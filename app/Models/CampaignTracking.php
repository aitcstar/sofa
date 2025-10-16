<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CampaignTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'action',
        'tracking_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    // العلاقات
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByCampaign($query, $campaignId)
    {
        return $query->where('campaign_id', $campaignId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getActionTextAttribute()
    {
        return match($this->action) {
            'sent' => 'تم الإرسال',
            'delivered' => 'تم التسليم',
            'opened' => 'تم الفتح',
            'clicked' => 'تم النقر',
            'unsubscribed' => 'إلغاء الاشتراك',
            'converted' => 'تم التحويل',
            default => $this->action
        };
    }

    public function getActionIconAttribute()
    {
        return match($this->action) {
            'sent' => 'send',
            'delivered' => 'check',
            'opened' => 'mail-open',
            'clicked' => 'mouse-pointer',
            'unsubscribed' => 'user-x',
            'converted' => 'shopping-cart',
            default => 'activity'
        };
    }

    public function getActionColorAttribute()
    {
        return match($this->action) {
            'sent' => 'blue',
            'delivered' => 'green',
            'opened' => 'yellow',
            'clicked' => 'purple',
            'unsubscribed' => 'red',
            'converted' => 'green',
            default => 'gray'
        };
    }

    // Methods
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
    public static function track($campaignId, $userId, $action, $metadata = [])
    {
        return static::create([
            'campaign_id' => $campaignId,
            'user_id' => $userId,
            'action' => $action,
            'tracking_id' => static::generateTrackingId(),
            'metadata' => $metadata
        ]);
    }

    public static function generateTrackingId()
    {
        return Str::uuid()->toString();
    }

    public static function getActionOptions()
    {
        return [
            'sent' => 'تم الإرسال',
            'delivered' => 'تم التسليم',
            'opened' => 'تم الفتح',
            'clicked' => 'تم النقر',
            'unsubscribed' => 'إلغاء الاشتراك',
            'converted' => 'تم التحويل'
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tracking) {
            if (!$tracking->tracking_id) {
                $tracking->tracking_id = static::generateTrackingId();
            }
        });

        static::created(function ($tracking) {
            // تحديث إحصائيات الحملة
            $tracking->campaign->updateStats();
        });
    }
}
