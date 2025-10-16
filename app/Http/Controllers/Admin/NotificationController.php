<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display notifications listing.
     */
    public function index(Request $request)
    {
        $query = Notification::with('notifiable');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('read_status')) {
            if ($request->read_status === 'read') {
                $query->whereNotNull('read_at');
            } else {
                $query->whereNull('read_at');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = Notification::getStatistics();

        return view('admin.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Show notification details.
     */
    public function show(Notification $notification)
    {
        $notification->load('notifiable');
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Show create notification form.
     */
    public function create()
    {
        $users = User::all();
        $templates = NotificationTemplate::active()->get();
        
        return view('admin.notifications.create', compact('users', 'templates'));
    }

    /**
     * Store new notification.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'recipient_type' => 'required|in:user,all_users,role',
            'recipient_id' => 'required_if:recipient_type,user',
            'role' => 'required_if:recipient_type,role',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'channel' => 'required|in:database,email,sms,whatsapp',
            'priority' => 'required|integer|between:1,4',
            'send_immediately' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $recipients = $this->getRecipients($request);

            foreach ($recipients as $recipient) {
                $notification = Notification::createNotification(
                    $request->type,
                    $recipient,
                    $request->title,
                    $request->message,
                    $request->data ? json_decode($request->data, true) : null,
                    $request->channel,
                    $request->priority
                );

                if ($request->send_immediately) {
                    $this->notificationService->sendNotification($notification);
                }
            }

            return redirect()->route('admin.notifications.index')
                ->with('success', 'تم إنشاء الإشعارات بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الإشعارات: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mark notifications as read.
     */
    public function markAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $count = Notification::whereIn('id', $request->notification_ids)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => "تم تحديد {$count} إشعار كمقروء",
        ]);
    }

    /**
     * Send test notification.
     */
    public function sendTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'channel' => 'required|in:email,sms,whatsapp',
            'recipient' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Create a test user object
            $testUser = new User([
                'name' => 'Test User',
                'email' => $request->channel === 'email' ? $request->recipient : 'test@example.com',
                'phone' => $request->channel !== 'email' ? $request->recipient : '1234567890',
            ]);

            $notification = Notification::createNotification(
                'test',
                $testUser,
                'رسالة تجريبية',
                $request->message,
                null,
                $request->channel,
                2
            );

            $result = $this->notificationService->sendNotification($notification);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال الرسالة التجريبية بنجاح',
                ]);
            } else {
                return response()->json([
                    'error' => 'فشل في إرسال الرسالة التجريبية',
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'حدث خطأ أثناء إرسال الرسالة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unread notifications count.
     */
    public function unread()
    {
        $count = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        $notifications = Notification::getRecentForUser(auth()->user(), 5);

        return response()->json([
            'count' => $count,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->formatted_type,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'is_read' => !is_null($notification->read_at),
                ];
            }),
        ]);
    }

    /**
     * Display notification templates.
     */
    public function templates()
    {
        $templates = NotificationTemplate::orderBy('type')->paginate(20);
        return view('admin.notifications.templates.index', compact('templates'));
    }

    /**
     * Show create template form.
     */
    public function createTemplate()
    {
        return view('admin.notifications.templates.create');
    }

    /**
     * Store new template.
     */
    public function storeTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'channel' => 'required|in:email,sms,whatsapp,database',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'language' => 'required|string|max:5',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        NotificationTemplate::create($request->all());

        return redirect()->route('admin.notifications.templates.index')
            ->with('success', 'تم إنشاء القالب بنجاح');
    }

    /**
     * Show edit template form.
     */
    public function editTemplate(NotificationTemplate $template)
    {
        return view('admin.notifications.templates.edit', compact('template'));
    }

    /**
     * Update template.
     */
    public function updateTemplate(Request $request, NotificationTemplate $template)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'channel' => 'required|in:email,sms,whatsapp,database',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'language' => 'required|string|max:5',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $template->update($request->all());

        return redirect()->route('admin.notifications.templates.index')
            ->with('success', 'تم تحديث القالب بنجاح');
    }

    /**
     * Delete template.
     */
    public function destroyTemplate(NotificationTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.notifications.templates.index')
            ->with('success', 'تم حذف القالب بنجاح');
    }

    /**
     * Get recipients based on request parameters.
     */
    private function getRecipients(Request $request)
    {
        switch ($request->recipient_type) {
            case 'user':
                return [User::findOrFail($request->recipient_id)];
                
            case 'all_users':
                return User::all();
                
            case 'role':
                return User::where('role', $request->role)->get();
                
            default:
                throw new \InvalidArgumentException('Invalid recipient type');
        }
    }
}
