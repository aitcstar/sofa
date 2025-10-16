<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ScheduledTask;
use App\Models\User;
use App\Models\OrderLog;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutomationService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Check for overdue orders and send notifications.
     */
    public function checkOverdueOrders(): int
    {
        $overdueOrders = Order::overdue()->get();
        $notified = 0;

        foreach ($overdueOrders as $order) {
            // Check if we already sent overdue notification today
            $alreadyNotified = OrderLog::where('order_id', $order->id)
                ->where('action', 'overdue_notification')
                ->whereDate('created_at', today())
                ->exists();

            if (!$alreadyNotified) {
                // Send notification to customer
                if ($order->user) {
                    $this->notificationService->sendOrderNotification($order, 'order_overdue');
                }

                // Send notification to assigned employee
                if ($order->assignedEmployee) {
                    $this->notificationService->send(
                        'order_overdue',
                        $order->assignedEmployee,
                        [
                            'title' => 'طلب متأخر',
                            'message' => "الطلب رقم {$order->order_number} متأخر عن موعد التسليم",
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'customer_name' => $order->name,
                            'days_overdue' => abs($order->getDaysUntilDelivery()),
                        ],
                        ['database', 'email']
                    );
                }

                // Send notification to admins
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $this->notificationService->send(
                        'order_overdue',
                        $admin,
                        [
                            'title' => 'طلب متأخر',
                            'message' => "الطلب رقم {$order->order_number} متأخر عن موعد التسليم",
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'customer_name' => $order->name,
                            'days_overdue' => abs($order->getDaysUntilDelivery()),
                        ],
                        ['database']
                    );
                }

                // Log the notification
                OrderLog::createLog(
                    $order->id,
                    'overdue_notification',
                    null,
                    'sent',
                    'تم إرسال تنبيه تأخير الطلب'
                );

                $notified++;
            }
        }

        return $notified;
    }

    /**
     * Send pending order reminders.
     */
    public function sendPendingOrderReminders(): int
    {
        $pendingOrders = Order::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(48))
            ->get();

        $reminded = 0;

        foreach ($pendingOrders as $order) {
            // Check if we already sent reminder today
            $alreadyReminded = OrderLog::where('order_id', $order->id)
                ->where('action', 'pending_reminder')
                ->whereDate('created_at', today())
                ->exists();

            if (!$alreadyReminded) {
                // Send reminder to admins
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $this->notificationService->send(
                        'order_reminder',
                        $admin,
                        [
                            'title' => 'تذكير بطلب معلق',
                            'message' => "الطلب رقم {$order->order_number} معلق منذ أكثر من 48 ساعة",
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'customer_name' => $order->name,
                            'hours_pending' => $order->created_at->diffInHours(now()),
                        ],
                        ['database']
                    );
                }

                // Send reminder to assigned employee if any
                if ($order->assignedEmployee) {
                    $this->notificationService->send(
                        'order_reminder',
                        $order->assignedEmployee,
                        [
                            'title' => 'تذكير بطلب معلق',
                            'message' => "الطلب رقم {$order->order_number} معلق منذ أكثر من 48 ساعة",
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'customer_name' => $order->name,
                            'hours_pending' => $order->created_at->diffInHours(now()),
                        ],
                        ['database', 'email']
                    );
                }

                // Log the reminder
                OrderLog::createLog(
                    $order->id,
                    'pending_reminder',
                    null,
                    'sent',
                    'تم إرسال تذكير بالطلب المعلق'
                );

                $reminded++;
            }
        }

        return $reminded;
    }

    /**
     * Auto-assign orders to employees based on workload.
     */
    public function autoAssignOrders(): int
    {
        $unassignedOrders = Order::whereNull('assigned_to')
            ->where('status', 'confirmed')
            ->get();

        $assigned = 0;

        foreach ($unassignedOrders as $order) {
            $employee = $this->findBestEmployeeForOrder($order);
            
            if ($employee) {
                $order->update(['assigned_to' => $employee->id]);

                // Log the assignment
                OrderLog::createLog(
                    $order->id,
                    'auto_assigned',
                    null,
                    $employee->name,
                    "تم التعيين التلقائي للموظف {$employee->name}"
                );

                // Notify the employee
                $this->notificationService->send(
                    'employee_assigned',
                    $employee,
                    [
                        'title' => 'تعيين طلب جديد',
                        'message' => "تم تعيينك للطلب رقم {$order->order_number}",
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->name,
                    ],
                    ['database', 'email']
                );

                $assigned++;
            }
        }

        return $assigned;
    }

    /**
     * Update order statuses based on timeline progress.
     */
    public function updateOrderStatusesFromTimeline(): int
    {
        $orders = Order::with('timeline')->get();
        $updated = 0;

        foreach ($orders as $order) {
            $newStatus = $this->calculateStatusFromTimeline($order);
            
            if ($newStatus && $newStatus !== $order->status) {
                $oldStatus = $order->status;
                $order->update(['status' => $newStatus]);

                // Log the status change
                OrderLog::createLog(
                    $order->id,
                    'auto_status_update',
                    $oldStatus,
                    $newStatus,
                    'تم تحديث الحالة تلقائياً بناءً على التايم لاين'
                );

                // Send notification
                $this->notificationService->notifyOrderStatusChanged($order, $oldStatus);

                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Send payment reminders for overdue invoices.
     */
    public function sendPaymentReminders(): int
    {
        $ordersWithOverduePayments = Order::where('payment_status', '!=', 'paid')
            ->where('total_amount', '>', 0)
            ->whereNotNull('expected_delivery_date')
            ->where('expected_delivery_date', '<', now()->subDays(7))
            ->get();

        $reminded = 0;

        foreach ($ordersWithOverduePayments as $order) {
            // Check if we already sent payment reminder this week
            $alreadyReminded = OrderLog::where('order_id', $order->id)
                ->where('action', 'payment_reminder')
                ->where('created_at', '>=', now()->subWeek())
                ->exists();

            if (!$alreadyReminded && $order->user) {
                $this->notificationService->sendOrderNotification($order, 'payment_reminder', [
                    'remaining_amount' => number_format($order->remaining_amount, 2),
                    'due_date' => $order->expected_delivery_date->format('Y-m-d'),
                ]);

                // Log the reminder
                OrderLog::createLog(
                    $order->id,
                    'payment_reminder',
                    null,
                    'sent',
                    'تم إرسال تذكير بالدفع'
                );

                $reminded++;
            }
        }

        return $reminded;
    }

    /**
     * Clean up old data and logs.
     */
    public function cleanupOldData(): array
    {
        $results = [];

        // Clean old order logs (older than 6 months)
        $deletedLogs = OrderLog::where('created_at', '<', now()->subMonths(6))->delete();
        $results['deleted_logs'] = $deletedLogs;

        // Clean old notifications (older than 3 months and read)
        $deletedNotifications = \App\Models\Notification::where('created_at', '<', now()->subMonths(3))
            ->whereNotNull('read_at')
            ->delete();
        $results['deleted_notifications'] = $deletedNotifications;

        // Clean completed scheduled tasks (older than 1 month)
        $deletedTasks = ScheduledTask::where('status', 'completed')
            ->where('updated_at', '<', now()->subMonth())
            ->delete();
        $results['deleted_tasks'] = $deletedTasks;

        return $results;
    }

    /**
     * Generate daily reports.
     */
    public function generateDailyReports(): bool
    {
        try {
            $yesterday = now()->subDay();
            
            $reportData = [
                'date' => $yesterday->format('Y-m-d'),
                'new_orders' => Order::whereDate('created_at', $yesterday)->count(),
                'completed_orders' => Order::whereDate('delivered_at', $yesterday)->count(),
                'total_revenue' => Order::whereDate('delivered_at', $yesterday)->sum('total_amount'),
                'overdue_orders' => Order::overdue()->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
            ];

            // Send report to admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $this->notificationService->send(
                    'daily_report',
                    $admin,
                    [
                        'title' => 'التقرير اليومي - ' . $yesterday->format('Y-m-d'),
                        'message' => $this->formatDailyReportMessage($reportData),
                        'report_data' => $reportData,
                    ],
                    ['email']
                );
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to generate daily report: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process scheduled tasks.
     */
    public function processScheduledTasks(): int
    {
        $dueTasks = ScheduledTask::getDueTasks();
        $processed = 0;

        foreach ($dueTasks as $task) {
            try {
                $task->markAsRunning();
                
                $result = $this->executeTask($task);
                
                if ($result) {
                    $task->markAsCompleted($result);
                    $processed++;
                } else {
                    $task->markAsFailed('Task execution returned false');
                }

            } catch (\Exception $e) {
                $task->markAsFailed($e->getMessage());
                Log::error("Scheduled task {$task->id} failed: " . $e->getMessage());
            }
        }

        return $processed;
    }

    /**
     * Execute a specific scheduled task.
     */
    private function executeTask(ScheduledTask $task): bool
    {
        return match($task->type) {
            'check_overdue_orders' => $this->checkOverdueOrders() > 0,
            'send_pending_reminders' => $this->sendPendingOrderReminders() > 0,
            'send_payment_reminders' => $this->sendPaymentReminders() > 0,
            'auto_assign_orders' => $this->autoAssignOrders() > 0,
            'update_order_statuses' => $this->updateOrderStatusesFromTimeline() > 0,
            'cleanup_old_data' => !empty($this->cleanupOldData()),
            'generate_daily_report' => $this->generateDailyReports(),
            default => false
        };
    }

    /**
     * Find the best employee for an order based on workload.
     */
    private function findBestEmployeeForOrder(Order $order): ?User
    {
        $employees = User::where('role', 'employee')->get();
        
        if ($employees->isEmpty()) {
            return null;
        }

        // Calculate workload for each employee
        $employeeWorkloads = [];
        
        foreach ($employees as $employee) {
            $activeOrders = Order::where('assigned_to', $employee->id)
                ->whereNotIn('status', ['delivered', 'cancelled', 'archived'])
                ->count();
            
            $employeeWorkloads[$employee->id] = $activeOrders;
        }

        // Find employee with minimum workload
        $minWorkload = min($employeeWorkloads);
        $bestEmployeeId = array_search($minWorkload, $employeeWorkloads);

        return User::find($bestEmployeeId);
    }

    /**
     * Calculate order status based on timeline progress.
     */
    private function calculateStatusFromTimeline(Order $order): ?string
    {
        $timeline = $order->timeline;
        
        if ($timeline->isEmpty()) {
            return null;
        }

        $completedStages = $timeline->where('status', 'completed')->count();
        $totalStages = $timeline->count();
        
        if ($completedStages === 0) {
            return 'pending';
        } elseif ($completedStages < $totalStages) {
            return 'processing';
        } else {
            return 'shipped'; // All stages completed, ready for shipping
        }
    }

    /**
     * Format daily report message.
     */
    private function formatDailyReportMessage(array $data): string
    {
        return "التقرير اليومي لتاريخ {$data['date']}:\n\n" .
               "• طلبات جديدة: {$data['new_orders']}\n" .
               "• طلبات مكتملة: {$data['completed_orders']}\n" .
               "• إجمالي الإيرادات: " . number_format($data['total_revenue'], 2) . " ر.س\n" .
               "• طلبات متأخرة: {$data['overdue_orders']}\n" .
               "• طلبات معلقة: {$data['pending_orders']}";
    }

    /**
     * Create default scheduled tasks.
     */
    public function createDefaultScheduledTasks(): void
    {
        $tasks = [
            [
                'name' => 'فحص الطلبات المتأخرة',
                'type' => 'check_overdue_orders',
                'frequency' => 'daily',
                'scheduled_at' => now()->setTime(9, 0), // 9 AM daily
                'description' => 'فحص الطلبات المتأخرة وإرسال التنبيهات',
            ],
            [
                'name' => 'تذكير بالطلبات المعلقة',
                'type' => 'send_pending_reminders',
                'frequency' => 'daily',
                'scheduled_at' => now()->setTime(10, 0), // 10 AM daily
                'description' => 'إرسال تذكيرات بالطلبات المعلقة أكثر من 48 ساعة',
            ],
            [
                'name' => 'تذكير بالدفعات',
                'type' => 'send_payment_reminders',
                'frequency' => 'weekly',
                'scheduled_at' => now()->next(Carbon::MONDAY)->setTime(11, 0), // Monday 11 AM
                'description' => 'إرسال تذكيرات بالدفعات المتأخرة',
            ],
            [
                'name' => 'تنظيف البيانات القديمة',
                'type' => 'cleanup_old_data',
                'frequency' => 'weekly',
                'scheduled_at' => now()->next(Carbon::SUNDAY)->setTime(2, 0), // Sunday 2 AM
                'description' => 'تنظيف السجلات والإشعارات القديمة',
            ],
            [
                'name' => 'التقرير اليومي',
                'type' => 'generate_daily_report',
                'frequency' => 'daily',
                'scheduled_at' => now()->setTime(23, 0), // 11 PM daily
                'description' => 'إنشاء وإرسال التقرير اليومي',
            ],
        ];

        foreach ($tasks as $taskData) {
            ScheduledTask::createTask(
                $taskData['name'],
                $taskData['type'],
                $taskData['frequency'],
                $taskData['scheduled_at'],
                null,
                $taskData['description']
            );
        }
    }
}
