<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'ip_address',
        'user_agent',
        'location',
        'risk_level',
        'description',
        'metadata',
        'is_suspicious',
        'occurred_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_suspicious' => 'boolean',
        'occurred_at' => 'datetime'
    ];

    // العلاقات
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByRiskLevel($query, $riskLevel)
    {
        return $query->where('risk_level', $riskLevel);
    }

    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    public function scopeByIpAddress($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('occurred_at', '>=', now()->subHours($hours));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('occurred_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('occurred_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    // Accessors
    public function getEventTypeTextAttribute()
    {
        return match($this->event_type) {
            'login' => 'تسجيل دخول',
            'logout' => 'تسجيل خروج',
            'failed_login' => 'فشل تسجيل دخول',
            'password_change' => 'تغيير كلمة المرور',
            'password_reset' => 'إعادة تعيين كلمة المرور',
            'account_locked' => 'قفل الحساب',
            'account_unlocked' => 'إلغاء قفل الحساب',
            'profile_update' => 'تحديث الملف الشخصي',
            'permission_change' => 'تغيير الصلاحيات',
            'suspicious_activity' => 'نشاط مشبوه',
            'data_access' => 'الوصول للبيانات',
            'data_modification' => 'تعديل البيانات',
            'data_deletion' => 'حذف البيانات',
            'admin_action' => 'إجراء إداري',
            'api_access' => 'الوصول للAPI',
            'file_upload' => 'رفع ملف',
            'file_download' => 'تحميل ملف',
            default => $this->event_type
        };
    }

    public function getRiskLevelTextAttribute()
    {
        return match($this->risk_level) {
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'critical' => 'حرج',
            default => $this->risk_level
        };
    }

    public function getRiskLevelColorAttribute()
    {
        return match($this->risk_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }

    public function getEventIconAttribute()
    {
        return match($this->event_type) {
            'login' => 'log-in',
            'logout' => 'log-out',
            'failed_login' => 'x-circle',
            'password_change' => 'key',
            'password_reset' => 'refresh-cw',
            'account_locked' => 'lock',
            'account_unlocked' => 'unlock',
            'profile_update' => 'user',
            'permission_change' => 'shield',
            'suspicious_activity' => 'alert-triangle',
            'data_access' => 'eye',
            'data_modification' => 'edit',
            'data_deletion' => 'trash-2',
            'admin_action' => 'settings',
            'api_access' => 'server',
            'file_upload' => 'upload',
            'file_download' => 'download',
            default => 'activity'
        };
    }

    public function getBrowserAttribute()
    {
        if (!$this->user_agent) {
            return null;
        }

        // استخراج اسم المتصفح من User Agent
        if (strpos($this->user_agent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($this->user_agent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($this->user_agent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($this->user_agent, 'Edge') !== false) {
            return 'Edge';
        } elseif (strpos($this->user_agent, 'Opera') !== false) {
            return 'Opera';
        }

        return 'غير معروف';
    }

    public function getDeviceTypeAttribute()
    {
        if (!$this->user_agent) {
            return null;
        }

        if (strpos($this->user_agent, 'Mobile') !== false) {
            return 'جوال';
        } elseif (strpos($this->user_agent, 'Tablet') !== false) {
            return 'لوحي';
        }

        return 'سطح المكتب';
    }

    // Methods
    public function markAsSuspicious($reason = null)
    {
        $this->is_suspicious = true;

        if ($reason) {
            $metadata = $this->metadata ?: [];
            $metadata['suspicious_reason'] = $reason;
            $this->metadata = $metadata;
        }

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
    public static function log($eventType, $description, $userId = null, $riskLevel = 'low', $metadata = [])
    {
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();

        return static::create([
            'user_id' => $userId,
            'event_type' => $eventType,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'risk_level' => $riskLevel,
            'description' => $description,
            'metadata' => $metadata,
            'occurred_at' => now()
        ]);
    }

    public static function logLogin($userId, $successful = true)
    {
        $eventType = $successful ? 'login' : 'failed_login';
        $riskLevel = $successful ? 'low' : 'medium';
        $description = $successful ? 'تسجيل دخول ناجح' : 'فشل في تسجيل الدخول';

        return static::log($eventType, $description, $userId, $riskLevel);
    }

    public static function logLogout($userId)
    {
        return static::log('logout', 'تسجيل خروج', $userId, 'low');
    }

    public static function logPasswordChange($userId)
    {
        return static::log('password_change', 'تم تغيير كلمة المرور', $userId, 'medium');
    }

    public static function logSuspiciousActivity($description, $userId = null, $metadata = [])
    {
        return static::log('suspicious_activity', $description, $userId, 'high', $metadata);
    }

    public static function logDataAccess($description, $userId, $metadata = [])
    {
        return static::log('data_access', $description, $userId, 'low', $metadata);
    }

    public static function logDataModification($description, $userId, $metadata = [])
    {
        return static::log('data_modification', $description, $userId, 'medium', $metadata);
    }

    public static function logAdminAction($description, $userId, $metadata = [])
    {
        return static::log('admin_action', $description, $userId, 'medium', $metadata);
    }

    public static function getFailedLoginAttempts($ipAddress, $hours = 1)
    {
        return static::where('event_type', 'failed_login')
                     ->where('ip_address', $ipAddress)
                     ->where('occurred_at', '>=', now()->subHours($hours))
                     ->count();
    }

    public static function getSuspiciousIpAddresses($limit = 10)
    {
        return static::selectRaw('ip_address, COUNT(*) as suspicious_count')
                     ->where('is_suspicious', true)
                     ->groupBy('ip_address')
                     ->orderBy('suspicious_count', 'desc')
                     ->limit($limit)
                     ->get();
    }

    public static function getSecuritySummary($days = 7)
    {
        $startDate = now()->subDays($days);

        return [
            'total_events' => static::where('occurred_at', '>=', $startDate)->count(),
            'suspicious_events' => static::suspicious()->where('occurred_at', '>=', $startDate)->count(),
            'failed_logins' => static::byEventType('failed_login')->where('occurred_at', '>=', $startDate)->count(),
            'successful_logins' => static::byEventType('login')->where('occurred_at', '>=', $startDate)->count(),
            'high_risk_events' => static::byRiskLevel('high')->where('occurred_at', '>=', $startDate)->count(),
            'critical_events' => static::byRiskLevel('critical')->where('occurred_at', '>=', $startDate)->count(),
            'unique_ips' => static::where('occurred_at', '>=', $startDate)->distinct('ip_address')->count(),
            'top_event_types' => static::selectRaw('event_type, COUNT(*) as count')
                                      ->where('occurred_at', '>=', $startDate)
                                      ->groupBy('event_type')
                                      ->orderBy('count', 'desc')
                                      ->limit(5)
                                      ->get()
        ];
    }

    public static function getEventTypeOptions()
    {
        return [
            'login' => 'تسجيل دخول',
            'logout' => 'تسجيل خروج',
            'failed_login' => 'فشل تسجيل دخول',
            'password_change' => 'تغيير كلمة المرور',
            'password_reset' => 'إعادة تعيين كلمة المرور',
            'account_locked' => 'قفل الحساب',
            'account_unlocked' => 'إلغاء قفل الحساب',
            'profile_update' => 'تحديث الملف الشخصي',
            'permission_change' => 'تغيير الصلاحيات',
            'suspicious_activity' => 'نشاط مشبوه',
            'data_access' => 'الوصول للبيانات',
            'data_modification' => 'تعديل البيانات',
            'data_deletion' => 'حذف البيانات',
            'admin_action' => 'إجراء إداري',
            'api_access' => 'الوصول للAPI',
            'file_upload' => 'رفع ملف',
            'file_download' => 'تحميل ملف'
        ];
    }

    public static function getRiskLevelOptions()
    {
        return [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'critical' => 'حرج'
        ];
    }


    // Enhanced Methods
    public static function logOrderAction($action, $orderId, $userId, $oldValues = null, $newValues = null)
    {
        return static::log('order_' . $action, "تم {$action} الطلب #{$orderId}", $userId, 'medium', [
            'order_id' => $orderId,
            'old_data' => $oldValues,
            'new_data' => $newValues
        ]);
    }

    public static function logPaymentAction($action, $paymentId, $userId, $amount = null)
    {
        return static::log('payment_' . $action, "تم {$action} الدفعة #{$paymentId}", $userId, 'high', [
            'payment_id' => $paymentId,
            'amount' => $amount
        ]);
    }

    public static function logCouponAction($action, $couponCode, $userId)
    {
        return static::log('coupon_' . $action, "تم {$action} الكوبون {$couponCode}", $userId, 'medium', [
            'coupon_code' => $couponCode
        ]);
    }

    public static function logCampaignAction($action, $campaignId, $userId)
    {
        return static::log('campaign_' . $action, "تم {$action} الحملة #{$campaignId}", $userId, 'medium', [
            'campaign_id' => $campaignId
        ]);
    }

    public static function logFileAction($action, $fileName, $userId, $fileSize = null)
    {
        return static::log('file_' . $action, "تم {$action} الملف {$fileName}", $userId, 'low', [
            'file_name' => $fileName,
            'file_size' => $fileSize
        ]);
    }

    public static function logSystemAction($action, $description, $userId)
    {
        return static::log('system_' . $action, $description, $userId, 'high');
    }

    public static function logUnauthorizedAccess($resource, $userId = null)
    {
        return static::log('unauthorized_access', "محاولة وصول غير مصرح إلى {$resource}", $userId, 'high', [
            'resource' => $resource
        ]);
    }

    public static function logSecurityBreach($description, $metadata = [])
    {
        return static::log('security_breach', $description, null, 'critical', $metadata);
    }

    // Enhanced Statistics
    public static function getEnhancedSecuritySummary($days = 7)
    {
        $startDate = now()->subDays($days);

        $summary = static::getSecuritySummary($days);

        // Add enhanced metrics
        $summary['order_actions'] = static::where('occurred_at', '>=', $startDate)
            ->where('event_type', 'like', 'order_%')
            ->count();

        $summary['payment_actions'] = static::where('occurred_at', '>=', $startDate)
            ->where('event_type', 'like', 'payment_%')
            ->count();

        $summary['coupon_actions'] = static::where('occurred_at', '>=', $startDate)
            ->where('event_type', 'like', 'coupon_%')
            ->count();

        $summary['campaign_actions'] = static::where('occurred_at', '>=', $startDate)
            ->where('event_type', 'like', 'campaign_%')
            ->count();

        $summary['file_actions'] = static::where('occurred_at', '>=', $startDate)
            ->where('event_type', 'like', 'file_%')
            ->count();

        $summary['system_actions'] = static::where('occurred_at', '>=', $startDate)
            ->where('event_type', 'like', 'system_%')
            ->count();

        $summary['unauthorized_attempts'] = static::where('occurred_at', '>=', $startDate)
            ->where('event_type', 'unauthorized_access')
            ->count();

        $summary['security_breaches'] = static::where('occurred_at', '>=', $startDate)
            ->where('event_type', 'security_breach')
            ->count();

        return $summary;
    }

    public static function getTopRiskyUsers($days = 30, $limit = 10)
    {
        return static::selectRaw('user_id, COUNT(*) as risk_count')
            ->where('occurred_at', '>=', now()->subDays($days))
            ->whereIn('risk_level', ['high', 'critical'])
            ->whereNotNull('user_id')
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderBy('risk_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getSecurityTrends($days = 30)
    {
        return static::selectRaw('DATE(occurred_at) as date,
                                 COUNT(*) as total_events,
                                 SUM(CASE WHEN risk_level = "high" THEN 1 ELSE 0 END) as high_risk,
                                 SUM(CASE WHEN risk_level = "critical" THEN 1 ELSE 0 END) as critical_risk,
                                 SUM(CASE WHEN is_suspicious = 1 THEN 1 ELSE 0 END) as suspicious')
            ->where('occurred_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public static function getIpAnalysis($days = 7)
    {
        return [
            'total_unique_ips' => static::where('occurred_at', '>=', now()->subDays($days))
                ->distinct('ip_address')
                ->count(),
            'suspicious_ips' => static::where('occurred_at', '>=', now()->subDays($days))
                ->where('is_suspicious', true)
                ->distinct('ip_address')
                ->count(),
            'blocked_ips' => static::getBlockedIps(),
            'top_active_ips' => static::selectRaw('ip_address, COUNT(*) as activity_count')
                ->where('occurred_at', '>=', now()->subDays($days))
                ->groupBy('ip_address')
                ->orderBy('activity_count', 'desc')
                ->limit(10)
                ->get(),
            'failed_login_ips' => static::selectRaw('ip_address, COUNT(*) as failed_count')
                ->where('occurred_at', '>=', now()->subDays($days))
                ->where('event_type', 'failed_login')
                ->groupBy('ip_address')
                ->orderBy('failed_count', 'desc')
                ->limit(10)
                ->get()
        ];
    }

    public static function getBlockedIps()
    {
        // Get IPs with more than 5 failed login attempts in the last 24 hours
        return static::selectRaw('ip_address, COUNT(*) as failed_count')
            ->where('event_type', 'failed_login')
            ->where('occurred_at', '>=', now()->subHours(24))
            ->groupBy('ip_address')
            ->having('failed_count', '>=', 5)
            ->pluck('ip_address')
            ->toArray();
    }

    public static function isIpBlocked($ipAddress)
    {
        return in_array($ipAddress, static::getBlockedIps());
    }

    public static function shouldBlockIp($ipAddress)
    {
        $failedAttempts = static::getFailedLoginAttempts($ipAddress, 24);
        return $failedAttempts >= 5;
    }

    public static function getRecentCriticalEvents($limit = 20)
    {
        return static::where('risk_level', 'critical')
            ->orWhere('event_type', 'security_breach')
            ->orWhere('event_type', 'unauthorized_access')
            ->with('user:id,name,email')
            ->orderBy('occurred_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function generateSecurityReport($days = 30)
    {
        return [
            'summary' => static::getEnhancedSecuritySummary($days),
            'trends' => static::getSecurityTrends($days),
            'ip_analysis' => static::getIpAnalysis($days),
            'risky_users' => static::getTopRiskyUsers($days),
            'critical_events' => static::getRecentCriticalEvents(50),
            'event_distribution' => static::selectRaw('event_type, COUNT(*) as count')
                ->where('occurred_at', '>=', now()->subDays($days))
                ->groupBy('event_type')
                ->orderBy('count', 'desc')
                ->get(),
            'risk_distribution' => static::selectRaw('risk_level, COUNT(*) as count')
                ->where('occurred_at', '>=', now()->subDays($days))
                ->groupBy('risk_level')
                ->get()
        ];
    }

    public static function cleanupOldLogs($daysToKeep = 365)
    {
        // Keep critical events longer
        $criticalCutoff = now()->subDays($daysToKeep * 2);
        $normalCutoff = now()->subDays($daysToKeep);

        $deletedCritical = static::where('occurred_at', '<', $criticalCutoff)
            ->where('risk_level', 'critical')
            ->delete();

        $deletedNormal = static::where('occurred_at', '<', $normalCutoff)
            ->where('risk_level', '!=', 'critical')
            ->delete();

        return $deletedCritical + $deletedNormal;
    }
}
