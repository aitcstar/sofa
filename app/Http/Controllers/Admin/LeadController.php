<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    /**
     * Display a listing of leads
     */
    public function index(Request $request)
    {
        $query = Lead::with(['assignedTo', 'activities' => function($q) {
            $q->latest()->limit(3);
        }])->orderBy('created_at', 'desc');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // فلترة حسب المصدر
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // فلترة حسب الموظف المعين
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        // العملاء المحتملين المتأخرين
        if ($request->filled('overdue') && $request->overdue == '1') {
            $query->overdue();
        }

        $leads = $query->paginate(20);

        // إحصائيات سريعة
        $stats = [
            'total' => Lead::count(),
            'new' => Lead::new()->count(),
            'contacted' => Lead::contacted()->count(),
            'qualified' => Lead::qualified()->count(),
            'won' => Lead::won()->count(),
            'lost' => Lead::lost()->count(),
            'overdue' => Lead::overdue()->count(),
            'high_priority' => Lead::highPriority()->count(),
            'total_value' => Lead::sum('estimated_value'),
            'avg_value' => Lead::avg('estimated_value')
        ];


        $employees = User::where('role', 'employee')->get();

        return view('admin.leads.index', compact('leads', 'stats', 'employees'));
    }

    /**
     * Show the form for creating a new lead
     */
    public function create()
    {
        $employees = User::where('role', 'employee')->get();
        $packages = Package::all();

        return view('admin.leads.create', compact('employees', 'packages'));
    }

    /**
     * Store a newly created lead
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'source' => 'required|in:website,phone,referral,social_media,advertisement',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_value' => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            $lead = Lead::create($request->all());

            // تسجيل النشاط الأولي
            $lead->logActivity('created', 'تم إنشاء عميل محتمل جديد', auth()->id());

            // إذا تم تعيين موظف، قم بتسجيل النشاط
            if ($request->assigned_to) {
                $lead->logActivity('assigned', "تم تعيين العميل المحتمل إلى " . $lead->assignedTo->name, auth()->id());
            }

            // جدولة متابعة أولية إذا لم يتم تحديد تاريخ
            if (!$request->next_follow_up_at) {
                $lead->scheduleFollowUp(now()->addDays(3), auth()->id());
            }

            DB::commit();

            return redirect()->route('admin.leads.show', $lead)
                           ->with('success', 'تم إنشاء العميل المحتمل بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء إنشاء العميل المحتمل: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified lead
     */
    public function show(Lead $lead)
    {
        $lead->load(['assignedTo', 'activities.user', 'quotes', 'orders']);

        // الأنشطة الأخيرة
        $recentActivities = $lead->activities()->with('user')->latest()->limit(10)->get();

        // الأنشطة المعلقة
        $pendingActivities = $lead->activities()->pending()->with('user')->orderBy('scheduled_at')->get();

        // الإحصائيات
        $stats = [
            'total_activities' => $lead->activities()->count(),
            'completed_activities' => $lead->activities()->completed()->count(),
            'pending_activities' => $lead->activities()->pending()->count(),
            'quotes_sent' => $lead->quotes()->count(),
            'conversion_rate' => $lead->conversion_rate
        ];

        return view('admin.leads.show', compact('lead', 'recentActivities', 'pendingActivities', 'stats'));
    }

    /**
     * Show the form for editing the specified lead
     */
    public function edit(Lead $lead)
    {
        $employees = User::where('role', 'employee')->get();
        $packages = Package::all();

        return view('admin.leads.edit', compact('lead', 'employees', 'packages'));
    }

    /**
     * Update the specified lead
     */
    public function update(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'source' => 'required|in:website,phone,referral,social_media,advertisement',
            'status' => 'required|in:new,contacted,qualified,proposal_sent,negotiation,won,lost',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_value' => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $oldStatus = $lead->status;
        $oldAssignee = $lead->assigned_to;

        $lead->update($request->all());

        // تسجيل تغيير الحالة
        if ($oldStatus !== $lead->status) {
            $lead->logActivity('status_changed', "تم تغيير الحالة من {$oldStatus} إلى {$lead->status}", auth()->id());
        }

        // تسجيل تغيير التعيين
        if ($oldAssignee !== $lead->assigned_to) {
            if ($lead->assigned_to) {
                $lead->logActivity('assigned', "تم تعيين العميل المحتمل إلى " . $lead->assignedTo->name, auth()->id());
            } else {
                $lead->logActivity('unassigned', "تم إلغاء تعيين العميل المحتمل", auth()->id());
            }
        }

        return redirect()->route('admin.leads.show', $lead)
                       ->with('success', 'تم تحديث العميل المحتمل بنجاح');
    }

    /**
     * Update lead status
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:new,contacted,qualified,proposal_sent,negotiation,won,lost',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $lead->updateStatus($request->status, auth()->id());

        if ($request->notes) {
            $lead->logActivity('note', $request->notes, auth()->id());
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة العميل المحتمل بنجاح'
        ]);
    }

    /**
     * Assign lead to employee
     */
    public function assign(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $lead->assignTo($request->assigned_to);

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين العميل المحتمل بنجاح'
        ]);
    }

    /**
     * Schedule follow up
     */
    public function scheduleFollowUp(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'follow_up_date' => 'required|date|after:now',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $lead->scheduleFollowUp($request->follow_up_date, auth()->id());

        if ($request->notes) {
            LeadActivity::createNote($lead->id, auth()->id(), 'ملاحظة متابعة', $request->notes);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم جدولة المتابعة بنجاح'
        ]);
    }

    /**
     * Add activity to lead
     */
    public function addActivity(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:call,email,meeting,note,task',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'duration' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $activityData = [
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'subject' => $request->subject,
            'description' => $request->description
        ];

        if (in_array($request->type, ['meeting', 'task']) && $request->scheduled_at) {
            $activityData['status'] = 'pending';
            $activityData['scheduled_at'] = $request->scheduled_at;
        } else {
            $activityData['status'] = 'completed';
            $activityData['completed_at'] = now();
        }

        if ($request->type === 'call' && $request->duration) {
            $activityData['metadata'] = ['duration' => $request->duration];
        }

        $activity = LeadActivity::create($activityData);

        // تحديث آخر تواصل
        $lead->updateLastContact();

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة النشاط بنجاح',
            'activity' => $activity->load('user')
        ]);
    }

    /**
     * Convert lead to customer
     */
    public function convertToCustomer(Lead $lead)
    {
        try {
            $customer = $lead->convertToCustomer();

            return response()->json([
                'success' => true,
                'message' => 'تم تحويل العميل المحتمل إلى عميل بنجاح',
                'customer_id' => $customer->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحويل: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Convert lead to order
     */
    public function convertToOrder(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'nullable|exists:packages,id',
            'total_amount' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $orderData = [];
            if ($request->package_id) {
                $orderData['package_id'] = $request->package_id;
            }
            if ($request->total_amount) {
                $orderData['total_amount'] = $request->total_amount;
            }

            $order = $lead->convertToOrder($orderData);

            return response()->json([
                'success' => true,
                'message' => 'تم تحويل العميل المحتمل إلى طلب بنجاح',
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحويل: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete lead
     */
    public function destroy(Lead $lead)
    {
        if ($lead->orders()->exists()) {
            return redirect()->route('admin.leads.index')
                           ->with('error', 'لا يمكن حذف عميل محتمل له طلبات مرتبطة');
        }

        $lead->delete();

        return redirect()->route('admin.leads.index')
                       ->with('success', 'تم حذف العميل المحتمل بنجاح');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,assign,update_status,schedule_follow_up',
            'leads' => 'required|array',
            'leads.*' => 'exists:leads,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $leads = Lead::whereIn('id', $request->leads)->get();
        $count = 0;

        foreach ($leads as $lead) {
            switch ($request->action) {
                case 'delete':
                    if (!$lead->orders()->exists()) {
                        $lead->delete();
                        $count++;
                    }
                    break;

                case 'assign':
                    if ($request->assigned_to) {
                        $lead->assignTo($request->assigned_to);
                        $count++;
                    }
                    break;

                case 'update_status':
                    if ($request->status) {
                        $lead->updateStatus($request->status, auth()->id());
                        $count++;
                    }
                    break;

                case 'schedule_follow_up':
                    if ($request->follow_up_date) {
                        $lead->scheduleFollowUp($request->follow_up_date, auth()->id());
                        $count++;
                    }
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "تم تنفيذ العملية على {$count} عميل محتمل بنجاح"
        ]);
    }

    /**
     * Get lead statistics
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $stats = [
            'total_leads' => Lead::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_leads' => Lead::new()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'converted_leads' => Lead::won()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'conversion_rate' => 0,
            'avg_lead_value' => Lead::whereBetween('created_at', [$startDate, $endDate])->avg('estimated_value'),
            'total_lead_value' => Lead::whereBetween('created_at', [$startDate, $endDate])->sum('estimated_value'),
            'leads_by_source' => Lead::whereBetween('created_at', [$startDate, $endDate])
                                    ->groupBy('source')
                                    ->selectRaw('source, COUNT(*) as count')
                                    ->get(),
            'leads_by_status' => Lead::whereBetween('created_at', [$startDate, $endDate])
                                    ->groupBy('status')
                                    ->selectRaw('status, COUNT(*) as count')
                                    ->get()
        ];

        // حساب معدل التحويل
        if ($stats['total_leads'] > 0) {
            $stats['conversion_rate'] = ($stats['converted_leads'] / $stats['total_leads']) * 100;
        }

        return response()->json($stats);
    }

    /**
     * Export leads
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $status = $request->get('status');
        $assigned_to = $request->get('assigned_to');

        $query = Lead::with(['assignedTo']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($assigned_to) {
            $query->where('assigned_to', $assigned_to);
        }

        $leads = $query->get();

        if ($format === 'pdf') {
            return $this->exportToPdf($leads);
        } else {
            return $this->exportToExcel($leads);
        }
    }

    // Helper Methods
    private function getStartDate($period)
    {
        return match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
    }

    private function exportToPdf($leads)
    {
        // تنفيذ تصدير PDF للعملاء المحتملين
    }

    private function exportToExcel($leads)
    {
        // تنفيذ تصدير Excel للعملاء المحتملين
    }
}
