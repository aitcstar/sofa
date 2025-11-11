<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SecurityLog;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        // Process the request
        $response = $next($request);

        // Log the action after processing
        $this->logAction($request, $response, $startTime);

        return $response;
    }

    /**
     * Log the action for audit purposes.
     */
    private function logAction(Request $request, Response $response, float $startTime): void
    {
        // Skip logging for certain routes
        if ($this->shouldSkipLogging($request)) {
            return;
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 2); // in milliseconds
        $user = Auth::user();

        $action = $this->determineAction($request);
        $riskLevel = $this->determineRiskLevel($request, $response);
        $description = $this->generateDescription($request, $response);

        $metadata = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
            'status_code' => $response->getStatusCode(),
            'execution_time_ms' => $executionTime,
            'request_size' => strlen($request->getContent()),
            'response_size' => strlen($response->getContent()),
            'parameters' => $this->sanitizeParameters($request->all()),
            'headers' => $this->sanitizeHeaders($request->headers->all()),
        ];

        // Add file information if files were uploaded
        if ($request->hasFile('*')) {
            $metadata['files'] = $this->getFileInfo($request);
        }

        // Add database query information if available
        if (config('app.debug')) {
            $metadata['queries_count'] = \DB::getQueryLog() ? count(\DB::getQueryLog()) : 0;
        }

        SecurityLog::log(
            $action,
            $description,
            $user?->id,
            $riskLevel,
            $metadata
        );
    }

    /**
     * Determine if logging should be skipped for this request.
     */
    private function shouldSkipLogging(Request $request): bool
    {
        $skipRoutes = [
            'debugbar.*',
            'telescope.*',
            'horizon.*',
            '_ignition.*',
        ];

        $routeName = $request->route()?->getName();

        if ($routeName) {
            foreach ($skipRoutes as $pattern) {
                if (fnmatch($pattern, $routeName)) {
                    return true;
                }
            }
        }

        // Skip static assets
        $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif','webp', 'svg', 'ico', 'woff', 'woff2', 'ttf'];
        $extension = pathinfo($request->path(), PATHINFO_EXTENSION);

        if (in_array(strtolower($extension), $staticExtensions)) {
            return true;
        }

        // Skip health checks and monitoring
        $skipPaths = [
            'health',
            'status',
            'ping',
            'metrics',
            'favicon.ico',
        ];

        foreach ($skipPaths as $path) {
            if (str_contains($request->path(), $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine the action type based on the request.
     */
    private function determineAction(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();

        // Authentication actions
        if (str_contains($path, 'login')) {
            return 'login_attempt';
        }

        if (str_contains($path, 'logout')) {
            return 'logout';
        }

        if (str_contains($path, 'register')) {
            return 'registration';
        }

        if (str_contains($path, 'password/reset')) {
            return 'password_reset';
        }

        // Admin actions
        if (str_starts_with($path, 'admin/')) {
            return match($method) {
                'GET' => 'admin_view',
                'POST' => 'admin_create',
                'PUT', 'PATCH' => 'admin_update',
                'DELETE' => 'admin_delete',
                default => 'admin_action'
            };
        }

        // API actions
        if (str_starts_with($path, 'api/')) {
            return 'api_' . strtolower($method);
        }

        // File operations
        if ($request->hasFile('*')) {
            return 'file_upload';
        }

        // Data export/import
        if (str_contains($path, 'export')) {
            return 'data_export';
        }

        if (str_contains($path, 'import')) {
            return 'data_import';
        }

        // Order operations
        if (str_contains($path, 'order')) {
            return match($method) {
                'GET' => 'order_view',
                'POST' => 'order_create',
                'PUT', 'PATCH' => 'order_update',
                'DELETE' => 'order_delete',
                default => 'order_action'
            };
        }

        // Payment operations
        if (str_contains($path, 'payment')) {
            return 'payment_' . strtolower($method);
        }

        // General CRUD operations
        return match($method) {
            'GET' => 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'action'
        };
    }

    /**
     * Determine the risk level of the action.
     */
    private function determineRiskLevel(Request $request, Response $response): string
    {
        $method = $request->method();
        $path = $request->path();
        $statusCode = $response->getStatusCode();

        // Critical risk actions
        $criticalActions = [
            'admin/users/delete',
            'admin/security',
            'admin/settings/system',
            'admin/database',
            'admin/backup',
        ];

        foreach ($criticalActions as $action) {
            if (str_contains($path, $action)) {
                return 'critical';
            }
        }

        // High risk conditions
        if ($statusCode >= 500) {
            return 'high'; // Server errors
        }

        if ($statusCode === 403 || $statusCode === 401) {
            return 'high'; // Unauthorized access attempts
        }

        if (in_array($method, ['DELETE']) && str_starts_with($path, 'admin/')) {
            return 'high'; // Admin deletions
        }

        if (str_contains($path, 'password') || str_contains($path, 'security')) {
            return 'high'; // Security-related actions
        }

        // Medium risk conditions
        if (in_array($method, ['POST', 'PUT', 'PATCH']) && str_starts_with($path, 'admin/')) {
            return 'medium'; // Admin modifications
        }

        if ($request->hasFile('*')) {
            return 'medium'; // File uploads
        }

        if (str_contains($path, 'export') || str_contains($path, 'import')) {
            return 'medium'; // Data operations
        }

        // Low risk (default)
        return 'low';
    }

    /**
     * Generate a human-readable description of the action.
     */
    private function generateDescription(Request $request, Response $response): string
    {
        $method = $request->method();
        $path = $request->path();
        $statusCode = $response->getStatusCode();
        $user = Auth::user();

        $description = '';

        // Add user information
        if ($user) {
            $description .= "المستخدم {$user->name} ";
        } else {
            $description .= "مستخدم غير مسجل ";
        }

        // Add action description
        $description .= match($method) {
            'GET' => 'عرض',
            'POST' => 'إنشاء',
            'PUT', 'PATCH' => 'تحديث',
            'DELETE' => 'حذف',
            default => 'تنفيذ عملية'
        };

        $description .= " في {$path}";

        // Add status information
        if ($statusCode >= 400) {
            $description .= " (فشل - كود {$statusCode})";
        } else {
            $description .= " (نجح - كود {$statusCode})";
        }

        return $description;
    }

    /**
     * Sanitize request parameters for logging.
     */
    private function sanitizeParameters(array $parameters): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'cvv',
            'ssn',
            'social_security',
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($parameters[$field])) {
                $parameters[$field] = '[REDACTED]';
            }
        }

        // Limit the size of parameters to prevent huge logs
        $maxLength = 1000;
        foreach ($parameters as $key => $value) {
            if (is_string($value) && strlen($value) > $maxLength) {
                $parameters[$key] = substr($value, 0, $maxLength) . '... [TRUNCATED]';
            }
        }

        return $parameters;
    }

    /**
     * Sanitize headers for logging.
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = [
            'authorization',
            'cookie',
            'x-api-key',
            'x-auth-token',
        ];

        $sanitized = [];

        foreach ($headers as $key => $value) {
            $lowerKey = strtolower($key);

            if (in_array($lowerKey, $sensitiveHeaders)) {
                $sanitized[$key] = '[REDACTED]';
            } else {
                // Only keep first value if array
                $sanitized[$key] = is_array($value) ? $value[0] ?? '' : $value;
            }
        }

        return $sanitized;
    }

    /**
     * Get information about uploaded files.
     */
    private function getFileInfo(Request $request): array
    {
        $fileInfo = [];

        foreach ($request->allFiles() as $key => $files) {
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $fileInfo[] = [
                        'field' => $key,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $file->getClientOriginalExtension(),
                    ];
                }
            }
        }

        return $fileInfo;
    }
}
