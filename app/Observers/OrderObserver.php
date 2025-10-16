<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\NotificationService;
use App\Services\TaskSchedulerService;

class OrderObserver
{
    private $notificationService;
    private $taskScheduler;

    public function __construct(NotificationService $notificationService, TaskSchedulerService $taskScheduler)
    {
        $this->notificationService = $notificationService;
        $this->taskScheduler = $taskScheduler;
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order)
    {
        // إرسال إشعار بالطلب الجديد
        $this->notificationService->notifyOrderCreated($order);
        
        // جدولة تذكيرات الطلب
        $this->taskScheduler->scheduleOrderReminders($order);
        
        // جدولة تذكيرات الدفع
        $this->taskScheduler->schedulePaymentReminders($order);
        
        // تسجيل النشاط
        $order->logActivity('created', 'تم إنشاء الطلب', auth()->id());
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order)
    {
        // فحص تغيير الحالة
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $this->notificationService->notifyOrderStatusChanged($order, $oldStatus);
        }

        // فحص تعيين موظف جديد
        if ($order->isDirty('assigned_to') && $order->assigned_to) {
            $employee = $order->assignedEmployee;
            if ($employee) {
                $this->notificationService->notifyEmployeeAssigned($order, $employee);
            }
        }

        // إعادة جدولة التذكيرات إذا تغير تاريخ التسليم
        if ($order->isDirty('expected_delivery_date')) {
            $this->taskScheduler->cancelOrderTasks($order);
            $this->taskScheduler->scheduleOrderReminders($order);
        }

        // تحديث آخر نشاط
        $order->last_activity_at = now();
    }

    /**
     * Handle the Order "deleting" event.
     */
    public function deleting(Order $order)
    {
        // إلغاء المهام المجدولة للطلب
        $this->taskScheduler->cancelOrderTasks($order);
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order)
    {
        // إعادة جدولة المهام عند استعادة الطلب
        $this->taskScheduler->scheduleOrderReminders($order);
        $this->taskScheduler->schedulePaymentReminders($order);
    }
}
