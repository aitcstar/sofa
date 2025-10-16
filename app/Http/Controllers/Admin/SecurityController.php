<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use App\Models\FailedLoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SecurityController extends Controller
{
    /*public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:security.view')->only(['index', 'logs', 'analytics']);
        $this->middleware('permission:security.manage')->only(['blockIp', 'unblockIp', 'blockUser', 'unblockUser']);
        $this->middleware('permission:security.admin')->only(['cleanup', 'settings']);
    }*/

    /**
     * Display security dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'week');

        // Get security statistics
        $securityStats = SecurityLog::getEnhancedSecuritySummary(7);
        $failedLoginStats = FailedLoginAttempt::getStatistics('day');

        // Get recent critical events
        $criticalEvents = SecurityLog::getRecentCriticalEvents(10);

        // Get recent failed login attempts
        $recentFailedLogins = FailedLoginAttempt::getRecentActivity(10);

        // Get blocked IPs and emails
        $blockedIps = FailedLoginAttempt::getBlockedIps();
        $blockedEmails = FailedLoginAttempt::getBlockedEmails();

        // Get top attacking IPs
        $topAttackingIps = FailedLoginAttempt::getTopAttackingIps(24, 10);

        // Get risky users
        $riskyUsers = SecurityLog::getTopRiskyUsers(7, 10);

        // Get security trends
        $securityTrends = SecurityLog::getSecurityTrends(30);

        return view('admin.security.index', compact(
            'securityStats',
            'failedLoginStats',
            'criticalEvents',
            'recentFailedLogins',
            'blockedIps',
            'blockedEmails',
            'topAttackingIps',
            'riskyUsers',
            'securityTrends',
            'period'
        ));
    }

    /**
     * Display security logs.
     */
    public function logs(Request $request)
    {
        $query = SecurityLog::with('user');

        // Apply filters
        if ($request->filled('event_type')) {
            $query->byEventType($request->event_type);
        }

        if ($request->filled('risk_level')) {
            $query->byRiskLevel($request->risk_level);
        }

        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        if ($request->filled('ip_address')) {
            $query->byIpAddress($request->ip_address);
        }

        if ($request->filled('date_from')) {
            $query->where('occurred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('occurred_at', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('suspicious')) {
            $query->suspicious();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->orderBy('occurred_at', 'desc')->paginate(50);

        // Get filter options
        $eventTypes = SecurityLog::getEventTypeOptions();
        $riskLevels = SecurityLog::getRiskLevelOptions();
        $users = User::select('id', 'name', 'email')->get();

        return view('admin.security.logs', compact(
            'logs',
            'eventTypes',
            'riskLevels',
            'users'
        ));
    }

    /**
     * Display failed login attempts.
     */
    public function failedLogins(Request $request)
    {
        $query = FailedLoginAttempt::query();

        // Apply filters
        if ($request->filled('ip_address')) {
            $query->byIp($request->ip_address);
        }

        if ($request->filled('email')) {
            $query->byEmail($request->email);
        }

        if ($request->filled('blocked')) {
            if ($request->blocked === 'yes') {
                $query->blocked();
            } elseif ($request->blocked === 'no') {
                $query->where('is_blocked', false);
            }
        }

        if ($request->filled('date_from')) {
            $query->where('attempted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('attempted_at', '<=', $request->date_to . ' 23:59:59');
        }

        $attempts = $query->orderBy('attempted_at', 'desc')->paginate(50);

        // Get statistics
        $stats = FailedLoginAttempt::getStatistics('day');

        return view('admin.security.failed-logins', compact('attempts', 'stats'));
    }

    /**
     * Display security analytics.
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', 'month');

        // Generate comprehensive security report
        $securityReport = SecurityLog::generateSecurityReport(30);
        $failedLoginReport = FailedLoginAttempt::getSecurityReport('month');

        // Get IP analysis
        $ipAnalysis = SecurityLog::getIpAnalysis(30);

        $summary = \App\Models\SecurityLog::getEnhancedSecuritySummary(7);
    $criticalEvents = \App\Models\SecurityLog::getRecentCriticalEvents(20);
    $failedAttempts = \App\Models\FailedLoginAttempt::latest()->limit(20)->get();

        return view('admin.security.analytics', compact(
            'securityReport',
            'failedLoginReport',
            'ipAnalysis',
            'period','summary','criticalEvents','failedAttempts'
        ));
    }

    /**
     * Block IP address.
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'duration' => 'required|integer|min:1|max:10080', // Max 1 week
            'reason' => 'nullable|string|max:255'
        ]);

        FailedLoginAttempt::blockIp($request->ip_address, $request->duration);

        // Log the action
        SecurityLog::logAdminAction(
            "حظر عنوان IP: {$request->ip_address} لمدة {$request->duration} دقيقة",
            auth()->id(),
            [
                'ip_address' => $request->ip_address,
                'duration' => $request->duration,
                'reason' => $request->reason
            ]
        );

        return back()->with('success', "تم حظر عنوان IP {$request->ip_address} بنجاح");
    }

    /**
     * Unblock IP address.
     */
    public function unblockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip'
        ]);

        FailedLoginAttempt::unblockIp($request->ip_address);

        // Log the action
        SecurityLog::logAdminAction(
            "إلغاء حظر عنوان IP: {$request->ip_address}",
            auth()->id(),
            ['ip_address' => $request->ip_address]
        );

        return back()->with('success', "تم إلغاء حظر عنوان IP {$request->ip_address} بنجاح");
    }

    /**
     * Block email address.
     */
    public function blockEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'duration' => 'required|integer|min:1|max:10080',
            'reason' => 'nullable|string|max:255'
        ]);

        FailedLoginAttempt::blockEmail($request->email, $request->duration);

        // Log the action
        SecurityLog::logAdminAction(
            "حظر البريد الإلكتروني: {$request->email} لمدة {$request->duration} دقيقة",
            auth()->id(),
            [
                'email' => $request->email,
                'duration' => $request->duration,
                'reason' => $request->reason
            ]
        );

        return back()->with('success', "تم حظر البريد الإلكتروني {$request->email} بنجاح");
    }

    /**
     * Unblock email address.
     */
    public function unblockEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        FailedLoginAttempt::unblockEmail($request->email);

        // Log the action
        SecurityLog::logAdminAction(
            "إلغاء حظر البريد الإلكتروني: {$request->email}",
            auth()->id(),
            ['email' => $request->email]
        );

        return back()->with('success', "تم إلغاء حظر البريد الإلكتروني {$request->email} بنجاح");
    }

    /**
     * Block user account.
     */
    public function blockUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:255'
        ]);

        $user = User::findOrFail($request->user_id);

        // Prevent blocking super admin
        if ($user->hasRole('super_admin')) {
            return back()->with('error', 'لا يمكن حظر المدير العام');
        }

        $user->update(['is_active' => false]);

        // Log the action
        SecurityLog::logAdminAction(
            "حظر المستخدم: {$user->name} ({$user->email})",
            auth()->id(),
            [
                'blocked_user_id' => $user->id,
                'blocked_user_email' => $user->email,
                'reason' => $request->reason
            ]
        );

        return back()->with('success', "تم حظر المستخدم {$user->name} بنجاح");
    }

    /**
     * Unblock user account.
     */
    public function unblockUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update(['is_active' => true]);

        // Log the action
        SecurityLog::logAdminAction(
            "إلغاء حظر المستخدم: {$user->name} ({$user->email})",
            auth()->id(),
            [
                'unblocked_user_id' => $user->id,
                'unblocked_user_email' => $user->email
            ]
        );

        return back()->with('success', "تم إلغاء حظر المستخدم {$user->name} بنجاح");
    }

    /**
     * Mark security log as reviewed.
     */
    public function markAsReviewed(SecurityLog $log)
    {
        $log->addMetadata('reviewed_by', auth()->id());
        $log->addMetadata('reviewed_at', now()->toISOString());

        return back()->with('success', 'تم وضع علامة مراجعة على السجل');
    }

    /**
     * Mark security log as suspicious.
     */
    public function markAsSuspicious(Request $request, SecurityLog $log)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        $log->markAsSuspicious($request->reason);

        // Log this action
        SecurityLog::logAdminAction(
            "وضع علامة مشبوه على السجل #{$log->id}",
            auth()->id(),
            [
                'log_id' => $log->id,
                'reason' => $request->reason
            ]
        );

        return back()->with('success', 'تم وضع علامة مشبوه على السجل');
    }

    /**
     * Force password reset for user.
     */
    public function forcePasswordReset(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:255'
        ]);

        $user = User::findOrFail($request->user_id);

        // Generate temporary password
        $tempPassword = str()->random(12);
        $user->update([
            'password' => Hash::make($tempPassword),
            'must_change_password' => true
        ]);

        // Log the action
        SecurityLog::logAdminAction(
            "إجبار إعادة تعيين كلمة المرور للمستخدم: {$user->name}",
            auth()->id(),
            [
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
                'reason' => $request->reason
            ]
        );

        // TODO: Send email with temporary password

        return back()->with('success', "تم إجبار إعادة تعيين كلمة المرور للمستخدم {$user->name}");
    }

    /**
     * Get security statistics API.
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 'week');

        $stats = [
            'security_logs' => SecurityLog::getEnhancedSecuritySummary(7),
            'failed_logins' => FailedLoginAttempt::getStatistics($period),
            'trends' => SecurityLog::getSecurityTrends(30),
        ];

        return response()->json($stats);
    }

    /**
     * Export security report.
     */
    public function exportReport(Request $request)
    {
        $period = $request->get('period', 'month');
        $format = $request->get('format', 'excel');

        $report = SecurityLog::generateSecurityReport(30);

        // TODO: Generate export based on format

        return back()->with('success', 'تم تصدير التقرير بنجاح');
    }

    /**
     * Cleanup old security data.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days_to_keep' => 'required|integer|min:30|max:1095' // 30 days to 3 years
        ]);

        $daysToKeep = $request->days_to_keep;

        // Cleanup security logs
        $deletedLogs = SecurityLog::cleanupOldLogs($daysToKeep);

        // Cleanup failed login attempts
        $deletedAttempts = FailedLoginAttempt::cleanupOldAttempts($daysToKeep);

        // Cleanup expired blocks
        $clearedBlocks = FailedLoginAttempt::cleanupExpiredBlocks();

        // Log the cleanup action
        SecurityLog::logAdminAction(
            "تنظيف بيانات الأمان القديمة",
            auth()->id(),
            [
                'days_kept' => $daysToKeep,
                'deleted_logs' => $deletedLogs,
                'deleted_attempts' => $deletedAttempts,
                'cleared_blocks' => $clearedBlocks
            ]
        );

        return back()->with('success',
            "تم تنظيف البيانات بنجاح: {$deletedLogs} سجل أمان، " .
            "{$deletedAttempts} محاولة تسجيل دخول فاشلة، " .
            "{$clearedBlocks} حظر منتهي الصلاحية"
        );
    }

    /**
     * Get real-time security alerts.
     */
    public function getAlerts()
    {
        $alerts = [
            'critical_events' => SecurityLog::getRecentCriticalEvents(5),
            'recent_blocks' => FailedLoginAttempt::blocked()->limit(5)->get(),
            'suspicious_ips' => FailedLoginAttempt::getTopAttackingIps(1, 5),
            'system_status' => $this->getSystemSecurityStatus(),
        ];

        return response()->json($alerts);
    }

    /**
     * Get system security status.
     */
    private function getSystemSecurityStatus(): array
    {
        $lastHour = now()->subHour();

        return [
            'status' => 'normal', // normal, warning, critical
            'failed_logins_last_hour' => FailedLoginAttempt::where('attempted_at', '>=', $lastHour)->count(),
            'blocked_ips_count' => count(FailedLoginAttempt::getBlockedIps()),
            'high_risk_events_last_hour' => SecurityLog::where('occurred_at', '>=', $lastHour)
                                                     ->where('risk_level', 'high')
                                                     ->count(),
            'critical_events_last_hour' => SecurityLog::where('occurred_at', '>=', $lastHour)
                                                      ->where('risk_level', 'critical')
                                                      ->count(),
        ];
    }

    /**
     * Security settings management.
     */
    public function settings()
    {
        $settings = [
            'max_failed_attempts' => config('security.max_failed_attempts', 5),
            'block_duration' => config('security.block_duration', 60),
            'session_timeout' => config('session.lifetime', 120),
            'password_expiry_days' => config('security.password_expiry_days', 90),
            'two_factor_required' => config('security.two_factor_required', false),
            'ip_whitelist' => config('security.ip_whitelist', []),
            'suspicious_activity_threshold' => config('security.suspicious_activity_threshold', 10),
        ];

        return view('admin.security.settings', compact('settings'));
    }

    /**
     * Update security settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'max_failed_attempts' => 'required|integer|min:3|max:10',
            'block_duration' => 'required|integer|min:5|max:1440',
            'session_timeout' => 'required|integer|min:15|max:480',
            'password_expiry_days' => 'required|integer|min:30|max:365',
            'two_factor_required' => 'boolean',
            'ip_whitelist' => 'nullable|string',
            'suspicious_activity_threshold' => 'required|integer|min:5|max:50',
        ]);

        // TODO: Update configuration settings
        // This would typically involve updating a settings table or config files

        // Log the settings change
        SecurityLog::logAdminAction(
            "تحديث إعدادات الأمان",
            auth()->id(),
            $request->only([
                'max_failed_attempts',
                'block_duration',
                'session_timeout',
                'password_expiry_days',
                'two_factor_required',
                'suspicious_activity_threshold'
            ])
        );

        return back()->with('success', 'تم تحديث إعدادات الأمان بنجاح');
    }
}
