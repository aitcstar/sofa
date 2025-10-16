<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Auth;
class NotificationService
{
    /**
     * إرسال إشعار جديد
     */
    public function send($type, $notifiable, $data = [], $channels = ['database'])
    {
        $notifications = [];

        foreach ($channels as $channel) {
            $notification = $this->createNotification($type, $notifiable, $data, $channel);

            if ($notification) {
                $this->sendNotification($notification);
                $notifications[] = $notification;
            }
        }

        return $notifications;
    }

    /**
     * إنشاء إشعار جديد
     */
    private function createNotification($type, $notifiable, $data, $channel)
    {
        $template = NotificationTemplate::where('type', $type)
                                      ->where('channel', $channel)
                                      ->active()
                                      ->first();

        if (!$template) {
            Log::warning("No template found for type: {$type}, channel: {$channel}");
            return null;
        }

        $priority = $data['priority'] ?? $this->getDefaultPriority($type);

        return Notification::create([
            'type' => $type,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'data' => array_merge($data, [
                'template_id' => $template->id,
                'subject' => $template->renderSubject($data),
                'content' => $template->renderContent($data)
            ]),
            'priority' => $priority,
            'channel' => $channel,
            'sent' => false
        ]);
    }

    /**
     * إرسال الإشعار حسب القناة
     */
    private function sendNotification(Notification $notification)
    {
        try {
            switch ($notification->channel) {
                case 'email':
                    $this->sendEmailNotification($notification);
                    break;
                case 'sms':
                    $this->sendSmsNotification($notification);
                    break;
                case 'whatsapp':
                    $this->sendWhatsAppNotification($notification);
                    break;
                case 'database':
                    // الإشعارات الداخلية لا تحتاج إرسال
                    $notification->markAsSent();
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Failed to send notification {$notification->id}: " . $e->getMessage());
        }
    }

    /**
     * إرسال إشعار بالبريد الإلكتروني
     */
    private function sendEmailNotification(Notification $notification)
    {
        if (!$notification->notifiable->email) {
            Log::warning("No email address for notification {$notification->id}");
            return;
        }

        Mail::send('emails.notification', [
            'notification' => $notification,
            'subject' => $notification->data['subject'],
            'content' => $notification->data['content']
        ], function ($message) use ($notification) {
            $message->to($notification->notifiable->email)
                   ->subject($notification->data['subject']);
        });

        $notification->markAsSent();
    }

    /**
     * إرسال رسالة نصية
     */
    private function sendSmsNotification(Notification $notification)
    {
        // تنفيذ إرسال SMS
        // يمكن استخدام خدمات مثل Twilio أو Nexmo

        $notification->markAsSent();
    }

    /**
     * إرسال رسالة واتساب
     */
    private function sendWhatsAppNotification(Notification $notification)
    {
        // تنفيذ إرسال WhatsApp
        // يمكن استخدام WhatsApp Business API

        $notification->markAsSent();
    }

    /**
     * إشعارات الطلبات
     */
    public function notifyOrderCreated(Order $order)
    {
        $data = [
            'title' => 'طلب جديد',
            'message' => "تم إنشاء طلب جديد رقم {$order->order_number}",
            'url' => route('admin.orders.enhanced.show', $order),
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->name,
            'package_name' => $order->package->name ?? '',
            'priority' => 'medium'
        ];

        // إشعار للأدمن
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->send('order_created', $admin, $data, ['database', 'email']);
        }

        // إشعار للعميل إذا كان مسجلاً
        if ($order->user) {
            $customerData = array_merge($data, [
                'title' => 'تأكيد الطلب',
                'message' => "تم استلام طلبكم رقم {$order->order_number} وسيتم التواصل معكم قريباً"
            ]);
            $this->send('order_created', $order->user, $customerData, ['email']);
        }
    }

    public function notifyOrderStatusChanged(Order $order, $oldStatus)
    {
        $data = [
            'title' => 'تغيير حالة الطلب',
            'message' => "تم تغيير حالة الطلب {$order->order_number} من {$oldStatus} إلى {$order->status}",
            'url' => route('admin.orders.enhanced.show', $order),
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'old_status' => $oldStatus,
            'new_status' => $order->status,
            'customer_name' => $order->name
        ];

        // إشعار للموظف المعين
        if ($order->assignedEmployee) {
            $this->send('order_status_changed', $order->assignedEmployee, $data, ['database']);
        }

        // إشعار للعميل
        if ($order->user) {
            $customerData = array_merge($data, [
                'message' => "تم تحديث حالة طلبكم رقم {$order->order_number} إلى: {$order->status_text}"
            ]);
            $this->send('order_status_changed', $order->user, $customerData, ['email']);
        }
    }

    public function notifyOrderOverdue(Order $order)
    {
        $data = [
            'title' => 'طلب متأخر',
            'message' => "الطلب رقم {$order->order_number} متأخر عن موعد التسليم المتوقع",
            'url' => route('admin.orders.show', $order),
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->name,
            'expected_date' => $order->expected_delivery_date?->format('Y-m-d'),
            'days_overdue' => $order->getDaysUntilDelivery(),
            'priority' => 'high'
        ];

        // إشعار للأدمن والموظف المعين
        $users = collect();

        if ($order->assignedEmployee) {
            $users->push($order->assignedEmployee);
        }

        $users = $users->merge(User::where('role', 'admin')->get());

        foreach ($users->unique('id') as $user) {
            $this->send('order_overdue', $user, $data, ['database', 'email']);
        }
    }

    public function notifyPaymentReceived(Order $order, $payment)
    {
        $data = [
            'title' => 'استلام دفعة',
            'message' => "تم استلام دفعة بقيمة {$payment->amount} ريال للطلب {$order->order_number}",
            'url' => route('admin.orders.show', $order),
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_amount' => $payment->amount,
            'payment_method' => $payment->payment_method_text,
            'customer_name' => $order->name
        ];

        // إشعار للأدمن
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->send('payment_received', $admin, $data, ['database']);
        }

        // إشعار للعميل
        if ($order->user) {
            $customerData = array_merge($data, [
                'message' => "تم استلام دفعتكم بقيمة {$payment->amount} ريال للطلب {$order->order_number}"
            ]);
            $this->send('payment_received', $order->user, $customerData, ['email']);
        }
    }

    public function notifyEmployeeAssigned(Order $order, User $employee)
    {
        $data = [
            'title' => 'تعيين طلب جديد',
            'message' => "تم تعيينك للطلب رقم {$order->order_number}",
            'url' => route('admin.orders.show', $order),
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->name,
            'package_name' => $order->package->name ?? ''
        ];

        $this->send('employee_assigned', $employee, $data, ['database', 'email']);
    }

    /**
     * إرسال تذكيرات دورية
     */
    public function sendPendingOrderReminders()
    {
        $pendingOrders = Order::where('status', 'pending')
                             ->where('created_at', '<', now()->subHours(48))
                             ->get();

        foreach ($pendingOrders as $order) {
            $data = [
                'title' => 'تذكير بطلب معلق',
                'message' => "الطلب رقم {$order->order_number} معلق منذ أكثر من 48 ساعة",
                'url' => route('admin.orders.show', $order),
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->name,
                'hours_pending' => $order->created_at->diffInHours(now()),
                'priority' => 'medium'
            ];

            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $this->send('order_reminder', $admin, $data, ['database']);
            }
        }
    }

    /**
     * الحصول على الأولوية الافتراضية حسب نوع الإشعار
     */
    private function getDefaultPriority($type)
    {
        return match($type) {
            'order_overdue' => 'high',
            'payment_received' => 'medium',
            'order_created' => 'medium',
            'system_alert' => 'urgent',
            default => 'medium'
        };
    }

    /**
     * الحصول على الإشعارات غير المقروءة للمستخدم
     */
    public function getUnreadNotifications($user, $limit = 10)
    {
         // التحقق من المستخدم الحالي
        $user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();

        // لو مفيش مستخدم مسجل دخول
        if (!$user) {
            return collect();
        }


        return Notification::where('notifiable_type', get_class($user))
                          ->where('notifiable_id', $user->id)
                          ->unread()
                          ->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * عدد الإشعارات غير المقروءة
     */
    public function getUnreadCount($user)
    {
        return Notification::where('notifiable_type', get_class($user))
                          ->where('notifiable_id', $user->id)
                          ->unread()
                          ->count();
    }

    /**
     * تمييز جميع الإشعارات كمقروءة
     */
    public function markAllAsRead($user)
    {
        return Notification::where('notifiable_type', get_class($user))
                          ->where('notifiable_id', $user->id)
                          ->unread()
                          ->update(['read_at' => now()]);
    }
}
