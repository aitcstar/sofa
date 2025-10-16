<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'email',
        'user_agent',
        'attempted_at',
        'is_blocked',
        'block_expires_at',
        'metadata'
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'is_blocked' => 'boolean',
        'block_expires_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Scopes
     */
    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true)
                    ->where('block_expires_at', '>', now());
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('attempted_at', '>=', now()->subHours($hours));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('attempted_at', today());
    }

    /**
     * Accessors
     */
    public function getIsCurrentlyBlockedAttribute(): bool
    {
        return $this->is_blocked && 
               $this->block_expires_at && 
               $this->block_expires_at > now();
    }

    public function getTimeUntilUnblockAttribute(): ?string
    {
        if (!$this->is_currently_blocked) {
            return null;
        }

        $diff = now()->diffInMinutes($this->block_expires_at);
        
        if ($diff < 60) {
            return "{$diff} دقيقة";
        }
        
        $hours = floor($diff / 60);
        $minutes = $diff % 60;
        
        return "{$hours} ساعة {$minutes} دقيقة";
    }

    public function getBrowserAttribute(): string
    {
        if (!$this->user_agent) {
            return 'غير معروف';
        }

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

    public function getDeviceTypeAttribute(): string
    {
        if (!$this->user_agent) {
            return 'غير معروف';
        }

        if (strpos($this->user_agent, 'Mobile') !== false) {
            return 'جوال';
        } elseif (strpos($this->user_agent, 'Tablet') !== false) {
            return 'تابلت';
        }

        return 'سطح المكتب';
    }

    /**
     * Methods
     */
    public function block(int $minutes = 60): void
    {
        $this->update([
            'is_blocked' => true,
            'block_expires_at' => now()->addMinutes($minutes)
        ]);
    }

    public function unblock(): void
    {
        $this->update([
            'is_blocked' => false,
            'block_expires_at' => null
        ]);
    }

    public function extendBlock(int $minutes = 60): void
    {
        if ($this->is_blocked) {
            $newExpiry = max(now()->addMinutes($minutes), $this->block_expires_at->addMinutes($minutes));
            $this->update(['block_expires_at' => $newExpiry]);
        } else {
            $this->block($minutes);
        }
    }

    /**
     * Static Methods
     */
    public static function recordAttempt(string $email, string $ip, string $userAgent = null): self
    {
        return static::create([
            'ip_address' => $ip,
            'email' => $email,
            'user_agent' => $userAgent ?: request()->userAgent(),
            'attempted_at' => now(),
            'metadata' => [
                'request_url' => request()->fullUrl(),
                'referer' => request()->header('referer'),
            ]
        ]);
    }

    public static function getAttemptsByIp(string $ip, int $hours = 24): int
    {
        return static::byIp($ip)
                    ->recent($hours)
                    ->count();
    }

    public static function getAttemptsByEmail(string $email, int $hours = 24): int
    {
        return static::byEmail($email)
                    ->recent($hours)
                    ->count();
    }

    public static function isIpBlocked(string $ip): bool
    {
        return static::byIp($ip)
                    ->blocked()
                    ->exists();
    }

    public static function isEmailBlocked(string $email): bool
    {
        return static::byEmail($email)
                    ->blocked()
                    ->exists();
    }

    public static function shouldBlockIp(string $ip, int $maxAttempts = 5, int $hours = 24): bool
    {
        return static::getAttemptsByIp($ip, $hours) >= $maxAttempts;
    }

    public static function shouldBlockEmail(string $email, int $maxAttempts = 3, int $hours = 24): bool
    {
        return static::getAttemptsByEmail($email, $hours) >= $maxAttempts;
    }

    public static function blockIp(string $ip, int $minutes = 60): void
    {
        static::byIp($ip)
              ->recent(24)
              ->update([
                  'is_blocked' => true,
                  'block_expires_at' => now()->addMinutes($minutes)
              ]);
    }

    public static function blockEmail(string $email, int $minutes = 60): void
    {
        static::byEmail($email)
              ->recent(24)
              ->update([
                  'is_blocked' => true,
                  'block_expires_at' => now()->addMinutes($minutes)
              ]);
    }

    public static function unblockIp(string $ip): void
    {
        static::byIp($ip)
              ->update([
                  'is_blocked' => false,
                  'block_expires_at' => null
              ]);
    }

    public static function unblockEmail(string $email): void
    {
        static::byEmail($email)
              ->update([
                  'is_blocked' => false,
                  'block_expires_at' => null
              ]);
    }

    public static function getBlockedIps(): array
    {
        return static::blocked()
                    ->distinct('ip_address')
                    ->pluck('ip_address')
                    ->toArray();
    }

    public static function getBlockedEmails(): array
    {
        return static::blocked()
                    ->distinct('email')
                    ->pluck('email')
                    ->toArray();
    }

    public static function getTopAttackingIps(int $hours = 24, int $limit = 10): array
    {
        return static::recent($hours)
                    ->selectRaw('ip_address, COUNT(*) as attempts')
                    ->groupBy('ip_address')
                    ->orderBy('attempts', 'desc')
                    ->limit($limit)
                    ->pluck('attempts', 'ip_address')
                    ->toArray();
    }

    public static function getTopTargetedEmails(int $hours = 24, int $limit = 10): array
    {
        return static::recent($hours)
                    ->selectRaw('email, COUNT(*) as attempts')
                    ->groupBy('email')
                    ->orderBy('attempts', 'desc')
                    ->limit($limit)
                    ->pluck('attempts', 'email')
                    ->toArray();
    }

    public static function getStatistics(string $period = 'day'): array
    {
        $startDate = match($period) {
            'hour' => now()->subHour(),
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            default => now()->subDay()
        };

        return [
            'total_attempts' => static::where('attempted_at', '>=', $startDate)->count(),
            'unique_ips' => static::where('attempted_at', '>=', $startDate)
                                 ->distinct('ip_address')
                                 ->count(),
            'unique_emails' => static::where('attempted_at', '>=', $startDate)
                                    ->distinct('email')
                                    ->count(),
            'blocked_ips' => static::blocked()->distinct('ip_address')->count(),
            'blocked_emails' => static::blocked()->distinct('email')->count(),
            'attempts_by_hour' => static::where('attempted_at', '>=', now()->subDay())
                                       ->selectRaw('HOUR(attempted_at) as hour, COUNT(*) as count')
                                       ->groupBy('hour')
                                       ->orderBy('hour')
                                       ->pluck('count', 'hour')
                                       ->toArray(),
            'top_attacking_ips' => static::getTopAttackingIps(24, 5),
            'top_targeted_emails' => static::getTopTargetedEmails(24, 5),
        ];
    }

    public static function cleanupExpiredBlocks(): int
    {
        return static::where('is_blocked', true)
                    ->where('block_expires_at', '<', now())
                    ->update([
                        'is_blocked' => false,
                        'block_expires_at' => null
                    ]);
    }

    public static function cleanupOldAttempts(int $daysToKeep = 30): int
    {
        return static::where('attempted_at', '<', now()->subDays($daysToKeep))
                    ->delete();
    }

    public static function getRecentActivity(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return static::with([])
                    ->orderBy('attempted_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    public static function getSecurityReport(string $period = 'week'): array
    {
        $stats = static::getStatistics($period);
        
        return [
            'statistics' => $stats,
            'recent_activity' => static::getRecentActivity(20),
            'blocked_ips' => static::getBlockedIps(),
            'blocked_emails' => static::getBlockedEmails(),
            'security_recommendations' => static::getSecurityRecommendations($stats),
        ];
    }

    public static function getSecurityRecommendations(array $stats): array
    {
        $recommendations = [];

        if ($stats['total_attempts'] > 100) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'عدد محاولات تسجيل الدخول الفاشلة مرتفع جداً',
                'action' => 'فكر في تقليل عدد المحاولات المسموحة أو زيادة مدة الحظر'
            ];
        }

        if ($stats['unique_ips'] > 50) {
            $recommendations[] = [
                'type' => 'info',
                'message' => 'هناك عدد كبير من عناوين IP المختلفة تحاول الوصول',
                'action' => 'راجع قائمة عناوين IP المشبوهة وفكر في حظرها'
            ];
        }

        if ($stats['blocked_ips'] > 20) {
            $recommendations[] = [
                'type' => 'success',
                'message' => 'نظام الحماية يعمل بفعالية',
                'action' => 'استمر في مراقبة النشاط المشبوه'
            ];
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'type' => 'success',
                'message' => 'مستوى الأمان جيد',
                'action' => 'استمر في المراقبة الدورية'
            ];
        }

        return $recommendations;
    }

    /**
     * Auto-block management
     */
    public static function checkAndBlock(string $email, string $ip): array
    {
        $result = [
            'ip_blocked' => false,
            'email_blocked' => false,
            'ip_attempts' => 0,
            'email_attempts' => 0,
        ];

        // Record the attempt
        static::recordAttempt($email, $ip);

        // Check IP attempts
        $ipAttempts = static::getAttemptsByIp($ip);
        $result['ip_attempts'] = $ipAttempts;

        if (static::shouldBlockIp($ip) && !static::isIpBlocked($ip)) {
            static::blockIp($ip, 60); // Block for 1 hour
            $result['ip_blocked'] = true;
        }

        // Check email attempts
        $emailAttempts = static::getAttemptsByEmail($email);
        $result['email_attempts'] = $emailAttempts;

        if (static::shouldBlockEmail($email) && !static::isEmailBlocked($email)) {
            static::blockEmail($email, 30); // Block for 30 minutes
            $result['email_blocked'] = true;
        }

        return $result;
    }

    /**
     * Get time remaining for block
     */
    public static function getBlockTimeRemaining(string $ip = null, string $email = null): ?int
    {
        $query = static::blocked();

        if ($ip) {
            $query->where('ip_address', $ip);
        }

        if ($email) {
            $query->where('email', $email);
        }

        $attempt = $query->orderBy('block_expires_at', 'desc')->first();

        if (!$attempt || !$attempt->block_expires_at) {
            return null;
        }

        return max(0, now()->diffInMinutes($attempt->block_expires_at, false));
    }
}
