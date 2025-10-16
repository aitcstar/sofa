<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SecurityLog;
use App\Models\FailedLoginAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        // Check if IP is blocked
        if ($this->isIpBlocked($request)) {
            SecurityLog::logUnauthorizedAccess('blocked_ip_attempt');
            return $this->blockedResponse('عنوان IP الخاص بك محظور مؤقتاً');
        }

        // Check for suspicious activity patterns
        if ($this->detectSuspiciousActivity($request)) {
            SecurityLog::logSuspiciousActivity(
                'نشاط مشبوه تم اكتشافه',
                Auth::id(),
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]
            );
        }

        // Apply rate limiting
        if ($this->isRateLimited($request)) {
            SecurityLog::logSuspiciousActivity(
                'تجاوز حد الطلبات المسموح',
                Auth::id(),
                ['ip' => $request->ip()]
            );
            return $this->rateLimitResponse();
        }

        // Log sensitive actions
        if ($this->isSensitiveAction($request)) {
            SecurityLog::logDataAccess(
                "الوصول إلى: {$request->path()}",
                Auth::id(),
                [
                    'method' => $request->method(),
                    'parameters' => $request->except(['password', 'password_confirmation', '_token'])
                ]
            );
        }

        $response = $next($request);

        // Add security headers
        $response = $this->addSecurityHeaders($response);

        return $response;
    }

    /**
     * Check if IP is blocked.
     */
    private function isIpBlocked(Request $request): bool
    {
        return FailedLoginAttempt::isIpBlocked($request->ip());
    }

    /**
     * Detect suspicious activity patterns.
     */
    private function detectSuspiciousActivity(Request $request): bool
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Check for rapid requests from same IP
        $recentRequests = SecurityLog::where('ip_address', $ip)
            ->where('occurred_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recentRequests > 50) {
            return true;
        }

        // Check for suspicious user agents
        $suspiciousAgents = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget',
            'python', 'java', 'perl', 'ruby', 'php'
        ];

        foreach ($suspiciousAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                return true;
            }
        }

        // Check for SQL injection patterns in URL
        $sqlPatterns = [
            'union', 'select', 'insert', 'update', 'delete', 'drop',
            'exec', 'script', 'javascript', 'vbscript', 'onload',
            'onerror', 'onclick', '<script', '</script>'
        ];

        $url = strtolower($request->fullUrl());
        foreach ($sqlPatterns as $pattern) {
            if (strpos($url, $pattern) !== false) {
                return true;
            }
        }

        // Check for XSS patterns
        $input = strtolower(implode(' ', $request->all()));
        foreach ($sqlPatterns as $pattern) {
            if (strpos($input, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if request should be rate limited.
     */
    private function isRateLimited(Request $request): bool
    {
        $key = 'security:' . $request->ip();
        
        // Allow 100 requests per minute for normal users
        $maxAttempts = Auth::check() ? 100 : 60;
        
        return RateLimiter::tooManyAttempts($key, $maxAttempts);
    }

    /**
     * Check if this is a sensitive action that should be logged.
     */
    private function isSensitiveAction(Request $request): bool
    {
        $sensitiveRoutes = [
            'admin/users',
            'admin/orders',
            'admin/payments',
            'admin/settings',
            'admin/security',
            'admin/reports',
            'admin/export',
        ];

        $path = $request->path();
        
        foreach ($sensitiveRoutes as $route) {
            if (strpos($path, $route) === 0) {
                return true;
            }
        }

        // Check for sensitive HTTP methods
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return true;
        }

        return false;
    }

    /**
     * Return blocked IP response.
     */
    private function blockedResponse(string $message): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => $message,
                'code' => 'IP_BLOCKED'
            ], 403);
        }

        return response()->view('errors.blocked', [
            'message' => $message
        ], 403);
    }

    /**
     * Return rate limit response.
     */
    private function rateLimitResponse(): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'تم تجاوز حد الطلبات المسموح',
                'code' => 'RATE_LIMITED'
            ], 429);
        }

        return response()->view('errors.rate-limited', [], 429);
    }

    /**
     * Add security headers to response.
     */
    private function addSecurityHeaders(Response $response): Response
    {
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}
