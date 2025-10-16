<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EnhancedOrderController;
use App\Http\Controllers\Admin\EnhancedFinancialController;
use App\Http\Controllers\Admin\EnhancedCRMController;
use App\Http\Controllers\Admin\EnhancedMarketingController;
use App\Http\Controllers\Admin\EnhancedEmployeeController;

/*
|--------------------------------------------------------------------------
| Enhanced System Routes
|--------------------------------------------------------------------------
|
| هذه Routes للنظام المحسن - يجب إضافتها إلى web.php
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // ==================== Enhanced Orders Routes ====================
    Route::prefix('orders/enhanced')->name('orders.enhanced.')->group(function () {
        Route::get('/', [EnhancedOrderController::class, 'index'])->name('index');
        Route::get('/create', [EnhancedOrderController::class, 'create'])->name('create');
        Route::post('/', [EnhancedOrderController::class, 'store'])->name('store');
        Route::get('/{order}', [EnhancedOrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [EnhancedOrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [EnhancedOrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [EnhancedOrderController::class, 'destroy'])->name('destroy');

        // Order Actions
        Route::post('/{order}/assign', [EnhancedOrderController::class, 'assignEmployee'])->name('assign');
        Route::post('/{order}/status', [EnhancedOrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/timeline', [EnhancedOrderController::class, 'addTimelineEvent'])->name('add-timeline');

        // Reports
        Route::get('/reports/index', [EnhancedOrderController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [EnhancedOrderController::class, 'exportOrders'])->name('export');
        Route::post('/reports/import', [EnhancedOrderController::class, 'importOrders'])->name('import');

        // Duplicate Detection
        Route::get('/duplicates', [EnhancedOrderController::class, 'detectDuplicates'])->name('duplicates');
    });

    // ==================== Financial Routes ====================
    Route::prefix('financial')->name('financial.')->group(function () {
        // Dashboard
        Route::get('/', [EnhancedFinancialController::class, 'index'])->name('index');

        // Invoices
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [EnhancedFinancialController::class, 'invoicesIndex'])->name('index');
            Route::get('/create', [EnhancedFinancialController::class, 'createInvoice'])->name('create');
            Route::post('/', [EnhancedFinancialController::class, 'storeInvoice'])->name('store');
            Route::get('/{invoice}', [EnhancedFinancialController::class, 'showInvoice'])->name('show');
            Route::get('/{invoice}/edit', [EnhancedFinancialController::class, 'editInvoice'])->name('edit');
            Route::put('/{invoice}', [EnhancedFinancialController::class, 'updateInvoice'])->name('update');
            Route::get('/{invoice}/download', [EnhancedFinancialController::class, 'downloadInvoice'])->name('download');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [EnhancedFinancialController::class, 'paymentsIndex'])->name('index');
            Route::get('/create', [EnhancedFinancialController::class, 'createPayment'])->name('create');
            Route::post('/', [EnhancedFinancialController::class, 'storePayment'])->name('store');
        });

        // Reports
        Route::get('/reports', [EnhancedFinancialController::class, 'reports'])->name('reports');
        Route::get('/export', [EnhancedFinancialController::class, 'export'])->name('export');
    });

    // ==================== CRM Routes ====================
    Route::prefix('crm')->name('crm.')->group(function () {
        // Dashboard
        Route::get('/', [EnhancedCRMController::class, 'index'])->name('index');

        // Leads
        Route::prefix('leads')->name('leads.')->group(function () {
            Route::get('/', [EnhancedCRMController::class, 'leadsIndex'])->name('index');
            Route::get('/create', [EnhancedCRMController::class, 'createLead'])->name('create');
            Route::post('/', [EnhancedCRMController::class, 'storeLead'])->name('store');
            Route::get('/{lead}', [EnhancedCRMController::class, 'showLead'])->name('show');
            Route::get('/{lead}/edit', [EnhancedCRMController::class, 'editLead'])->name('edit');
            Route::put('/{lead}', [EnhancedCRMController::class, 'updateLead'])->name('update');
            Route::post('/{lead}/activity', [EnhancedCRMController::class, 'addActivity'])->name('add-activity');
            Route::post('/{lead}/convert', [EnhancedCRMController::class, 'convertToOrder'])->name('convert');
        });

        // Quotes
        Route::prefix('quotes')->name('quotes.')->group(function () {
            Route::get('/', [EnhancedCRMController::class, 'quotesIndex'])->name('index');
            Route::get('/create', [EnhancedCRMController::class, 'createQuote'])->name('create');
            Route::post('/', [EnhancedCRMController::class, 'storeQuote'])->name('store');
            Route::get('/{quote}', [EnhancedCRMController::class, 'showQuote'])->name('show');
        });

        // Sales Funnel
        Route::get('/funnel', [EnhancedCRMController::class, 'funnel'])->name('funnel');

        // Activities
        Route::get('/activities', [EnhancedCRMController::class, 'activities'])->name('activities');
    });

    // ==================== Marketing Routes ====================
    Route::prefix('marketing')->name('marketing.')->group(function () {
        // Dashboard
        Route::get('/', [EnhancedMarketingController::class, 'index'])->name('index');

        // Campaigns
        Route::prefix('campaigns')->name('campaigns.')->group(function () {
            Route::get('/', [EnhancedMarketingController::class, 'campaigns'])->name('index');
            Route::get('/create', [EnhancedMarketingController::class, 'createCampaign'])->name('create');
            Route::post('/', [EnhancedMarketingController::class, 'storeCampaign'])->name('store');
            Route::get('/{campaign}', [EnhancedMarketingController::class, 'showCampaign'])->name('show');
            Route::get('/{campaign}/edit', [EnhancedMarketingController::class, 'editCampaign'])->name('edit');
            Route::put('/{campaign}', [EnhancedMarketingController::class, 'updateCampaign'])->name('update');
            Route::delete('/{campaign}', [EnhancedMarketingController::class, 'destroyCampaign'])->name('destroy');

            // Campaign Actions
            Route::post('/{campaign}/start', [EnhancedMarketingController::class, 'startCampaign'])->name('start');
            Route::post('/{campaign}/pause', [EnhancedMarketingController::class, 'pauseCampaign'])->name('pause');
            Route::post('/{campaign}/resume', [EnhancedMarketingController::class, 'resumeCampaign'])->name('resume');
            Route::post('/{campaign}/complete', [EnhancedMarketingController::class, 'completeCampaign'])->name('complete');
        });

        // Coupons
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [EnhancedMarketingController::class, 'coupons'])->name('index');
            Route::get('/create', [EnhancedMarketingController::class, 'createCoupon'])->name('create');
            Route::post('/', [EnhancedMarketingController::class, 'storeCoupon'])->name('store');
            Route::get('/{coupon}/edit', [EnhancedMarketingController::class, 'editCoupon'])->name('edit');
            Route::put('/{coupon}', [EnhancedMarketingController::class, 'updateCoupon'])->name('update');
            Route::delete('/{coupon}', [EnhancedMarketingController::class, 'destroyCoupon'])->name('destroy');
        });

        // Analytics
        Route::get('/analytics', [EnhancedMarketingController::class, 'analytics'])->name('analytics');
    });

    // ==================== Employee Management Routes ====================
    Route::prefix('employees')->name('employees.')->group(function () {
        // Employees
        Route::get('/', [EnhancedEmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EnhancedEmployeeController::class, 'create'])->name('create');
        Route::post('/', [EnhancedEmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EnhancedEmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EnhancedEmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EnhancedEmployeeController::class, 'update'])->name('update');
        Route::post('/{employee}/toggle', [EnhancedEmployeeController::class, 'toggleStatus'])->name('toggle');

        // Roles
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [EnhancedEmployeeController::class, 'rolesIndex'])->name('index');
            Route::get('/create', [EnhancedEmployeeController::class, 'createRole'])->name('create');
            Route::post('/', [EnhancedEmployeeController::class, 'storeRole'])->name('store');
            Route::get('/{role}/edit', [EnhancedEmployeeController::class, 'editRole'])->name('edit');
            Route::put('/{role}', [EnhancedEmployeeController::class, 'updateRole'])->name('update');
        });

        // Permissions
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/matrix', [EnhancedEmployeeController::class, 'permissionsMatrix'])->name('matrix');
            Route::post('/matrix', [EnhancedEmployeeController::class, 'updatePermissionsMatrix'])->name('update-matrix');
        });

        // Performance
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/reports', [EnhancedEmployeeController::class, 'performanceReports'])->name('reports');
        });
    });
});

// ==================== Employee Portal Routes ====================
Route::prefix('employee')->name('employee.')->middleware(['auth', 'employee'])->group(function () {
    Route::get('/dashboard', [EnhancedEmployeeController::class, 'employeeDashboard'])->name('dashboard');
    Route::get('/orders', [EnhancedEmployeeController::class, 'employeeOrders'])->name('orders');
    Route::get('/orders/{order}', [EnhancedEmployeeController::class, 'employeeOrderShow'])->name('orders.show');
    Route::post('/orders/{order}/status', [EnhancedEmployeeController::class, 'employeeUpdateOrderStatus'])->name('orders.update-status');
});

