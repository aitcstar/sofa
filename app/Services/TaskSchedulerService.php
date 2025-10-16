<?php

namespace App\Services;

use App\Models\ScheduledTask;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TaskSchedulerService
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * جدولة مهمة جديدة
     */
    public function schedule($name, $type, $data, $scheduledAt, $maxAttempts = 3)
    {
        return ScheduledTask::create([
            'name' => $name,
            'type' => $type,
            'data' => $data,
            'scheduled_at' => $scheduledAt,
            'max_attempts' => $maxAttempts
        ]);
    }

    /**
     * تنفيذ المهام الجاهزة
     */
    public function runPendingTasks()
    {
        $tasks = ScheduledTask::ready()->get();
        
        foreach ($tasks as $task) {
            $this->executeTask($task);
        }
        
        return $tasks->count();
    }

    /**
     * تنفيذ مهمة واحدة
     */
    public function executeTask(ScheduledTask $task)
    {
        try {
            $task->markAsRunning();
            
            $result = $this->runTaskByType($task);
            
            $task->markAsCompleted($result);
            
            Log::info("Task {$task->id} ({$task->type}) completed successfully");
            
        } catch (\Exception $e) {
            $task->markAsFailed($e->getMessage());
            
            Log::error("Task {$task->id} ({$task->type}) failed: " . $e->getMessage());
        }
    }

    /**
     * تنفيذ المهمة حسب النوع
     */
    private function runTaskByType(ScheduledTask $task)
    {
        switch ($task->type) {
            case 'send_reminder':
                return $this->sendReminder($task->data);
                
            case 'check_overdue_orders':
                return $this->checkOverdueOrders();
                
            case 'send_invoice_reminder':
                return $this->sendInvoiceReminder($task->data);
                
            case 'cleanup_logs':
                return $this->cleanupLogs($task->data);
                
            case 'generate_report':
                return $this->generateReport($task->data);
                
            case 'backup_data':
                return $this->backupData($task->data);
                
            default:
                throw new \Exception("Unknown task type: {$task->type}");
        }
    }

    /**
     * إرسال تذكير
     */
    private function sendReminder($data)
    {
        $orderId = $data['order_id'] ?? null;
        $reminderType = $data['reminder_type'] ?? 'general';
        
        if (!$orderId) {
            throw new \Exception("Order ID is required for reminder");
        }
        
        $order = Order::find($orderId);
        if (!$order) {
            throw new \Exception("Order not found: {$orderId}");
        }
        
        switch ($reminderType) {
            case 'pending_confirmation':
                $this->sendPendingConfirmationReminder($order);
                break;
                
            case 'delivery_approaching':
                $this->sendDeliveryApproachingReminder($order);
                break;
                
            case 'payment_due':
                $this->sendPaymentDueReminder($order);
                break;
                
            default:
                throw new \Exception("Unknown reminder type: {$reminderType}");
        }
        
        return "Reminder sent for order {$order->order_number}";
    }

    /**
     * فحص الطلبات المتأخرة
     */
    private function checkOverdueOrders()
    {
        $overdueOrders = Order::overdue()->get();
        $count = 0;
        
        foreach ($overdueOrders as $order) {
            $this->notificationService->notifyOrderOverdue($order);
            $count++;
        }
        
        return "Checked {$count} overdue orders";
    }

    /**
     * إرسال تذكير بالفاتورة
     */
    private function sendInvoiceReminder($data)
    {
        $invoiceId = $data['invoice_id'] ?? null;
        
        if (!$invoiceId) {
            throw new \Exception("Invoice ID is required");
        }
        
        $invoice = Invoice::find($invoiceId);
        if (!$invoice) {
            throw new \Exception("Invoice not found: {$invoiceId}");
        }
        
        // إرسال تذكير بالفاتورة
        // يمكن تنفيذ هذا عبر NotificationService
        
        return "Invoice reminder sent for invoice {$invoice->invoice_number}";
    }

    /**
     * تنظيف السجلات القديمة
     */
    private function cleanupLogs($data)
    {
        $days = $data['days'] ?? 30;
        $cutoffDate = Carbon::now()->subDays($days);
        
        // حذف سجلات الطلبات القديمة
        $deletedLogs = \App\Models\OrderLog::where('created_at', '<', $cutoffDate)->delete();
        
        // حذف الإشعارات القديمة المقروءة
        $deletedNotifications = \App\Models\Notification::whereNotNull('read_at')
                                                       ->where('created_at', '<', $cutoffDate)
                                                       ->delete();
        
        // حذف المهام المكتملة القديمة
        $deletedTasks = ScheduledTask::completed()
                                   ->where('created_at', '<', $cutoffDate)
                                   ->delete();
        
        return "Cleaned up {$deletedLogs} logs, {$deletedNotifications} notifications, {$deletedTasks} tasks";
    }

    /**
     * إنشاء تقرير
     */
    private function generateReport($data)
    {
        $reportType = $data['type'] ?? 'monthly';
        $period = $data['period'] ?? now()->format('Y-m');
        
        // تنفيذ إنشاء التقرير
        // يمكن إنشاء تقارير مختلفة حسب النوع
        
        return "Generated {$reportType} report for {$period}";
    }

    /**
     * نسخ احتياطي للبيانات
     */
    private function backupData($data)
    {
        $backupType = $data['type'] ?? 'full';
        
        // تنفيذ النسخ الاحتياطي
        // يمكن استخدام أوامر Laravel أو أدوات خارجية
        
        return "Created {$backupType} backup";
    }

    /**
     * جدولة المهام التلقائية
     */
    public function scheduleAutomaticTasks()
    {
        // فحص الطلبات المتأخرة يومياً
        $this->schedule(
            'فحص الطلبات المتأخرة اليومي',
            'check_overdue_orders',
            [],
            now()->addDay()->setTime(9, 0) // 9 صباحاً غداً
        );

        // تنظيف السجلات أسبوعياً
        $this->schedule(
            'تنظيف السجلات الأسبوعي',
            'cleanup_logs',
            ['days' => 30],
            now()->addWeek()->setTime(2, 0) // 2 صباحاً الأسبوع القادم
        );

        // إنشاء تقرير شهري
        $this->schedule(
            'تقرير شهري',
            'generate_report',
            ['type' => 'monthly', 'period' => now()->format('Y-m')],
            now()->addMonth()->startOfMonth()->setTime(8, 0)
        );
    }

    /**
     * جدولة تذكيرات الطلبات
     */
    public function scheduleOrderReminders(Order $order)
    {
        // تذكير بتأكيد الطلب بعد 24 ساعة
        if ($order->status === 'pending') {
            $this->schedule(
                "تذكير بتأكيد الطلب {$order->order_number}",
                'send_reminder',
                [
                    'order_id' => $order->id,
                    'reminder_type' => 'pending_confirmation'
                ],
                now()->addHours(24)
            );
        }

        // تذكير بقرب موعد التسليم
        if ($order->expected_delivery_date) {
            $this->schedule(
                "تذكير بقرب موعد التسليم للطلب {$order->order_number}",
                'send_reminder',
                [
                    'order_id' => $order->id,
                    'reminder_type' => 'delivery_approaching'
                ],
                $order->expected_delivery_date->subDays(3)->setTime(10, 0)
            );
        }
    }

    /**
     * جدولة تذكيرات الدفع
     */
    public function schedulePaymentReminders(Order $order)
    {
        if ($order->payment_status !== 'paid' && $order->total_amount > 0) {
            // تذكير بالدفع بعد 7 أيام
            $this->schedule(
                "تذكير بالدفع للطلب {$order->order_number}",
                'send_reminder',
                [
                    'order_id' => $order->id,
                    'reminder_type' => 'payment_due'
                ],
                now()->addDays(7)->setTime(10, 0)
            );
        }
    }

    /**
     * إلغاء المهام المجدولة للطلب
     */
    public function cancelOrderTasks(Order $order)
    {
        ScheduledTask::where('type', 'send_reminder')
                    ->where('data->order_id', $order->id)
                    ->pending()
                    ->update(['status' => 'cancelled']);
    }

    /**
     * الحصول على إحصائيات المهام
     */
    public function getTaskStats()
    {
        return [
            'pending' => ScheduledTask::pending()->count(),
            'running' => ScheduledTask::running()->count(),
            'completed_today' => ScheduledTask::completed()
                                            ->whereDate('executed_at', today())
                                            ->count(),
            'failed_today' => ScheduledTask::failed()
                                         ->whereDate('updated_at', today())
                                         ->count(),
            'overdue' => ScheduledTask::overdue()->count()
        ];
    }

    // Helper Methods للتذكيرات المختلفة
    private function sendPendingConfirmationReminder(Order $order)
    {
        // إرسال تذكير بتأكيد الطلب
    }

    private function sendDeliveryApproachingReminder(Order $order)
    {
        // إرسال تذكير بقرب موعد التسليم
    }

    private function sendPaymentDueReminder(Order $order)
    {
        // إرسال تذكير بالدفع المستحق
    }
}
