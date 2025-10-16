<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\LeadActivity;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CRMController extends Controller
{
    /**
     * Display CRM dashboard.
     */
    public function index()
    {
        $stats = $this->getCRMStats();
        $pipeline = Lead::getPipeline();
        $recentLeads = Lead::with(['assignedTo'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentQuotes = Quote::with(['lead', 'customer', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $overdueLeads = Lead::overdue()
            ->with(['assignedTo'])
            ->orderBy('next_follow_up_at')
            ->limit(5)
            ->get();

        $hotLeads = Lead::where('priority', 'urgent')
            ->whereNotIn('status', ['converted', 'lost'])
            ->with(['assignedTo'])
            ->orderBy('lead_score', 'desc')
            ->limit(5)
            ->get();

        return view('admin.crm.index', compact(
            'stats',
            'pipeline',
            'recentLeads',
            'recentQuotes',
            'overdueLeads',
            'hotLeads'
        ));
    }

    /**
     * Display leads management.
     */
    public function leads(Request $request)
    {
        $query = Lead::with(['assignedTo', 'activities']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('project_type')) {
            $query->where('project_type', $request->project_type);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(20);

        $employees = User::where('role', 'employee')->orderBy('name')->get();
        $statusOptions = Lead::getStatusOptions();
        $priorityOptions = Lead::getPriorityOptions();
        $sourceOptions = Lead::getSourceOptions();
        $projectTypeOptions = Lead::getProjectTypeOptions();

        return view('admin.crm.leads.index', compact(
            'leads',
            'employees',
            'statusOptions',
            'priorityOptions',
            'sourceOptions',
            'projectTypeOptions'
        ));
    }

    /**
     * Show lead details.
     */
    public function showLead(Lead $lead)
    {
        $lead->load(['assignedTo', 'activities.user', 'quotes', 'customer']);

        return view('admin.crm.leads.show', compact('lead'));
    }

    /**
     * Create new lead.
     */
    public function createLead(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:100',
            'source' => 'required|string',
            'priority' => 'required|string',
            'project_type' => 'nullable|string',
            'units_count' => 'nullable|integer|min:1',
            'budget_range' => 'nullable|string',
            'expected_start_date' => 'nullable|date',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $lead = Lead::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'position' => $request->position,
                'source' => $request->source,
                'status' => 'new',
                'priority' => $request->priority,
                'project_type' => $request->project_type,
                'units_count' => $request->units_count,
                'budget_range' => $request->budget_range,
                'expected_start_date' => $request->expected_start_date,
                'description' => $request->description,
                'assigned_to' => $request->assigned_to,
                'next_follow_up_at' => now()->addDays(1),
            ]);

            // Calculate initial lead score
            $lead->updateLeadScore();

            // Log initial activity
            $lead->logActivity('created', 'تم إنشاء العميل المحتمل', auth()->id());

            DB::commit();

            return redirect()
                ->route('admin.crm.leads.show', $lead)
                ->with('success', 'تم إنشاء العميل المحتمل بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء العميل المحتمل: ' . $e->getMessage());
        }
    }

    /**
     * Update lead.
     */
    public function updateLead(Lead $lead, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email,' . $lead->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:100',
            'source' => 'required|string',
            'priority' => 'required|string',
            'project_type' => 'nullable|string',
            'units_count' => 'nullable|integer|min:1',
            'budget_range' => 'nullable|string',
            'expected_start_date' => 'nullable|date',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $oldAssignee = $lead->assigned_to;

            $lead->update($request->all());

            // Log assignment change
            if ($oldAssignee != $request->assigned_to) {
                $newAssignee = $request->assigned_to ? User::find($request->assigned_to) : null;
                $lead->logActivity(
                    'assignment_changed',
                    $newAssignee ? "تم تعيين العميل المحتمل إلى {$newAssignee->name}" : 'تم إلغاء تعيين العميل المحتمل',
                    auth()->id()
                );
            }

            // Update lead score
            $lead->updateLeadScore();

            DB::commit();

            return redirect()
                ->route('admin.crm.leads.show', $lead)
                ->with('success', 'تم تحديث العميل المحتمل بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث العميل المحتمل: ' . $e->getMessage());
        }
    }

    /**
     * Update lead status.
     */
    public function updateLeadStatus(Lead $lead, Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:new,contacted,qualified,proposal_sent,negotiation,won,lost',
            'notes' => 'nullable|string',
        ]);

        try {
            $lead->updateStatus($request->status, auth()->id());

            if ($request->notes) {
                $lead->addNote($request->notes, auth()->id());
            }

            return back()->with('success', 'تم تحديث حالة العميل المحتمل بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث الحالة: ' . $e->getMessage());
        }
    }

    /**
     * Add note to lead.
     */
    public function addLeadNote(Lead $lead, Request $request)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        try {
            $lead->addNote($request->note, auth()->id());
            return back()->with('success', 'تم إضافة الملاحظة بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إضافة الملاحظة: ' . $e->getMessage());
        }
    }

    /**
     * Schedule follow-up for lead.
     */
    public function scheduleLeadFollowUp(Lead $lead, Request $request)
    {
        $request->validate([
            'follow_up_date' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        try {
            $lead->scheduleFollowUp(Carbon::parse($request->follow_up_date), auth()->id());

            if ($request->notes) {
                $lead->addNote($request->notes, auth()->id());
            }

            return back()->with('success', 'تم جدولة المتابعة بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء جدولة المتابعة: ' . $e->getMessage());
        }
    }

    /**
     * Convert lead to customer.
     */
    public function convertLeadToCustomer(Lead $lead, Request $request)
    {
        try {
            $customer = $lead->convertToCustomer($request->all());

            if ($customer) {
                return redirect()
                    ->route('admin.customers.show', $customer)
                    ->with('success', 'تم تحويل العميل المحتمل إلى عميل بنجاح');
            } else {
                return back()->with('error', 'فشل في تحويل العميل المحتمل');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء التحويل: ' . $e->getMessage());
        }
    }

    /**
     * Mark lead as lost.
     */
    public function markLeadAsLost(Lead $lead, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            $lead->markAsLost($request->reason, auth()->id());
            return back()->with('success', 'تم تحديد العميل المحتمل كمفقود');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث الحالة: ' . $e->getMessage());
        }
    }

    /**
     * Display quotes management.
     */
    public function quotes(Request $request)
    {
        $query = Quote::with(['lead', 'customer', 'createdBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhereHas('lead', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(20);

        $employees = User::where('role', 'employee')->orderBy('name')->get();
        $statusOptions = Quote::getStatusOptions();

        return view('admin.crm.quotes.index', compact('quotes', 'employees', 'statusOptions'));
    }

    /**
     * Show quote details.
     */
    public function showQuote(Quote $quote)
    {
        $quote->load(['lead', 'customer', 'createdBy', 'items']);

        return view('admin.crm.quotes.show', compact('quote'));
    }

    /**
     * Create quote from lead.
     */
    public function createQuoteFromLead(Lead $lead, Request $request)
    {
        try {
            DB::beginTransaction();

            $quote = Quote::createFromLead($lead, $request->all());

            DB::commit();

            return redirect()
                ->route('admin.crm.quotes.show', $quote)
                ->with('success', 'تم إنشاء عرض السعر بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء عرض السعر: ' . $e->getMessage());
        }
    }

    /**
     * Send quote.
     */
    public function sendQuote(Quote $quote)
    {
        try {
            $quote->send();
            return back()->with('success', 'تم إرسال عرض السعر بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إرسال عرض السعر: ' . $e->getMessage());
        }
    }

    /**
     * Accept quote.
     */
    public function acceptQuote(Quote $quote)
    {
        try {
            $quote->accept();
            return back()->with('success', 'تم قبول عرض السعر بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء قبول عرض السعر: ' . $e->getMessage());
        }
    }

    /**
     * Reject quote.
     */
    public function rejectQuote(Quote $quote, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        try {
            $quote->reject($request->reason);
            return back()->with('success', 'تم رفض عرض السعر');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء رفض عرض السعر: ' . $e->getMessage());
        }
    }

    /**
     * Convert quote to order.
     */
    public function convertQuoteToOrder(Quote $quote, Request $request)
    {
        try {
            $order = $quote->convertToOrder($request->all());

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'تم تحويل عرض السعر إلى طلب بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء التحويل: ' . $e->getMessage());
        }
    }

    /**
     * Get CRM reports.
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $reports = [
            'leads' => Lead::getStatistics($period),
            'quotes' => Quote::getStatistics($period),
            'pipeline' => Lead::getPipeline(),
            'sources' => Lead::getLeadsBySource(),
            'performance' => $this->getPerformanceReport($startDate, $endDate),
        ];

        return view('admin.crm.reports', compact('reports', 'period'));
    }

    /**
     * Export CRM data.
     */
    public function export(Request $request, string $type)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        switch ($type) {
            case 'leads':
                return $this->exportLeads($startDate, $endDate);
            case 'quotes':
                return $this->exportQuotes($startDate, $endDate);
            case 'pipeline':
                return $this->exportPipeline($startDate, $endDate);
            default:
                return back()->with('error', 'نوع التصدير غير مدعوم');
        }
    }

    /**
     * Get CRM statistics.
     */
    private function getCRMStats(): array
    {
        $today = today();
        $thisMonth = [now()->startOfMonth(), now()->endOfMonth()];

        return [
            'total_leads' => Lead::count(),
            'new_leads_today' => Lead::whereDate('created_at', $today)->count(),
            'hot_leads' => Lead::where('priority', 'urgent')
                ->whereNotIn('status', ['converted', 'lost'])
                ->count(),
            'overdue_leads' => Lead::overdue()->count(),
            'conversion_rate' => Lead::getConversionRate(),
            'total_quotes' => Quote::count(),
            'pending_quotes' => Quote::whereIn('status', ['sent', 'viewed'])->count(),
            'quote_acceptance_rate' => Quote::getAcceptanceRate(),
            'pipeline_value' => collect(Lead::getPipeline())->sum('value'),
        ];
    }

    /**
     * Get performance report.
     */
    private function getPerformanceReport($startDate, $endDate): array
    {
        $employees = User::where('role', 'employee')
            ->withCount([
                'assignedLeads as leads_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                },
                'assignedLeads as converted_leads_count' => function ($query) use ($startDate, $endDate) {
                    $query->where('status', 'converted')
                          ->whereBetween('converted_to_customer_at', [$startDate, $endDate]);
                }
            ])
            ->get();

        return $employees->map(function ($employee) {
            $conversionRate = $employee->leads_count > 0 ?
                round(($employee->converted_leads_count / $employee->leads_count) * 100, 2) : 0;

            return [
                'name' => $employee->name,
                'leads_count' => $employee->leads_count,
                'converted_leads_count' => $employee->converted_leads_count,
                'conversion_rate' => $conversionRate,
            ];
        })->toArray();
    }

    /**
     * Get start date based on period.
     */
    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
    }

    // Additional helper methods for exports would go here...
    private function exportLeads($startDate, $endDate)
    {
        // Implementation for exporting leads
    }

    private function exportQuotes($startDate, $endDate)
    {
        // Implementation for exporting quotes
    }

    private function exportPipeline($startDate, $endDate)
    {
        // Implementation for exporting pipeline data
    }

    public function activities(Request $request)
    {
        $query = LeadActivity::with(['lead', 'user']);

        // فلترة حسب نوع النشاط
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب الموظف
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(20);

        $employees = User::where('role', 'employee')->orderBy('name')->get();
        $types = LeadActivity::getTypeOptions(); // فرضًا عندك فانكشن أو تقدر تعمل مصفوفة ثابتة

        return view('admin.crm.activities.index', compact('activities', 'employees', 'types'));
    }


}
