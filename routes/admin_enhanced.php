<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EnhancedOrderController;
use App\Http\Controllers\Admin\OrderTimelineController;
use App\Http\Controllers\Admin\OrderAssignmentController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\EnhancedDashboardController;

/*
|--------------------------------------------------------------------------
| Enhanced Admin Routes
|--------------------------------------------------------------------------
|
| Here are the enhanced admin routes for the SOFA system.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {
    
    // Enhanced Dashboard
    Route::get('/dashboard/enhanced', [EnhancedDashboardController::class, 'index'])->name('dashboard.enhanced');
    Route::get('/dashboard/stats', [EnhancedDashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/charts', [EnhancedDashboardController::class, 'getCharts'])->name('dashboard.charts');

    // Enhanced Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/enhanced', [EnhancedOrderController::class, 'index'])->name('enhanced.index');
        Route::get('/enhanced/create', [EnhancedOrderController::class, 'create'])->name('enhanced.create');
        Route::post('/enhanced', [EnhancedOrderController::class, 'store'])->name('enhanced.store');
        Route::get('/enhanced/{order}', [EnhancedOrderController::class, 'show'])->name('enhanced.show');
        Route::put('/enhanced/{order}/status', [EnhancedOrderController::class, 'updateStatus'])->name('enhanced.update-status');
        Route::put('/enhanced/{order}/priority', [EnhancedOrderController::class, 'updatePriority'])->name('enhanced.update-priority');
        Route::post('/enhanced/{order}/assign', [EnhancedOrderController::class, 'assignEmployee'])->name('enhanced.assign');
        Route::post('/enhanced/{order}/note', [EnhancedOrderController::class, 'addNote'])->name('enhanced.add-note');
        Route::get('/enhanced/{order}/timeline', [EnhancedOrderController::class, 'getActivityTimeline'])->name('enhanced.timeline');
        Route::post('/enhanced/bulk-action', [EnhancedOrderController::class, 'bulkAction'])->name('enhanced.bulk-action');
        Route::get('/enhanced/export', [EnhancedOrderController::class, 'export'])->name('enhanced.export');
        
        // Order Timeline Management
        Route::prefix('{order}/timeline')->name('timeline.')->group(function () {
            Route::get('/', [OrderTimelineController::class, 'index'])->name('index');
            Route::post('/', [OrderTimelineController::class, 'store'])->name('store');
            Route::put('/{timeline}', [OrderTimelineController::class, 'update'])->name('update');
            Route::post('/{timeline}/start', [OrderTimelineController::class, 'start'])->name('start');
            Route::post('/{timeline}/complete', [OrderTimelineController::class, 'complete'])->name('complete');
            Route::put('/{timeline}/progress', [OrderTimelineController::class, 'updateProgress'])->name('update-progress');
        });

        // Order Assignments
        Route::prefix('{order}/assignments')->name('assignments.')->group(function () {
            Route::get('/', [OrderAssignmentController::class, 'index'])->name('index');
            Route::post('/', [OrderAssignmentController::class, 'store'])->name('store');
            Route::delete('/{assignment}', [OrderAssignmentController::class, 'destroy'])->name('destroy');
        });
    });

    // Notifications Management
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::put('/{notification}', [NotificationController::class, 'update'])->name('update');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/send-test', [NotificationController::class, 'sendTest'])->name('send-test');
        
        // Notification Templates
        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/', [NotificationController::class, 'templates'])->name('index');
            Route::get('/create', [NotificationController::class, 'createTemplate'])->name('create');
            Route::post('/', [NotificationController::class, 'storeTemplate'])->name('store');
            Route::get('/{template}/edit', [NotificationController::class, 'editTemplate'])->name('edit');
            Route::put('/{template}', [NotificationController::class, 'updateTemplate'])->name('update');
            Route::delete('/{template}', [NotificationController::class, 'destroyTemplate'])->name('destroy');
        });
    });

    // Financial Management
    Route::prefix('finance')->name('finance.')->group(function () {
        
        // Invoices
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
            Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
            Route::get('/{invoice}/pdf', [InvoiceController::class, 'generatePDF'])->name('pdf');
            Route::post('/{invoice}/send', [InvoiceController::class, 'sendByEmail'])->name('send');
            Route::post('/bulk-action', [InvoiceController::class, 'bulkAction'])->name('bulk-action');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::get('/create', [PaymentController::class, 'create'])->name('create');
            Route::post('/', [PaymentController::class, 'store'])->name('store');
            Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
            Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit');
            Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
            Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
            Route::post('/{payment}/confirm', [PaymentController::class, 'confirm'])->name('confirm');
            Route::get('/{payment}/receipt', [PaymentController::class, 'generateReceipt'])->name('receipt');
        });

        // Financial Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [FinancialReportsController::class, 'index'])->name('index');
            Route::get('/revenue', [FinancialReportsController::class, 'revenue'])->name('revenue');
            Route::get('/payments', [FinancialReportsController::class, 'payments'])->name('payments');
            Route::get('/outstanding', [FinancialReportsController::class, 'outstanding'])->name('outstanding');
            Route::get('/export/{type}', [FinancialReportsController::class, 'export'])->name('export');
        });
    });

    // CRM System
    Route::prefix('crm')->name('crm.')->group(function () {
        
        // Leads Management
        Route::prefix('leads')->name('leads.')->group(function () {
            Route::get('/', [LeadController::class, 'index'])->name('index');
            Route::get('/create', [LeadController::class, 'create'])->name('create');
            Route::post('/', [LeadController::class, 'store'])->name('store');
            Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
            Route::get('/{lead}/edit', [LeadController::class, 'edit'])->name('edit');
            Route::put('/{lead}', [LeadController::class, 'update'])->name('update');
            Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('destroy');
            Route::post('/{lead}/convert', [LeadController::class, 'convertToCustomer'])->name('convert');
            Route::post('/{lead}/activity', [LeadController::class, 'addActivity'])->name('add-activity');
            Route::put('/{lead}/status', [LeadController::class, 'updateStatus'])->name('update-status');
            Route::post('/{lead}/assign', [LeadController::class, 'assign'])->name('assign');
            Route::post('/bulk-action', [LeadController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/export', [LeadController::class, 'export'])->name('export');
        });

        // Quotes Management
        Route::prefix('quotes')->name('quotes.')->group(function () {
            Route::get('/', [QuoteController::class, 'index'])->name('index');
            Route::get('/create', [QuoteController::class, 'create'])->name('create');
            Route::post('/', [QuoteController::class, 'store'])->name('store');
            Route::get('/{quote}', [QuoteController::class, 'show'])->name('show');
            Route::get('/{quote}/edit', [QuoteController::class, 'edit'])->name('edit');
            Route::put('/{quote}', [QuoteController::class, 'update'])->name('update');
            Route::delete('/{quote}', [QuoteController::class, 'destroy'])->name('destroy');
            Route::get('/{quote}/pdf', [QuoteController::class, 'generatePDF'])->name('pdf');
            Route::post('/{quote}/send', [QuoteController::class, 'sendByEmail'])->name('send');
            Route::post('/{quote}/convert', [QuoteController::class, 'convertToOrder'])->name('convert');
            Route::post('/bulk-action', [QuoteController::class, 'bulkAction'])->name('bulk-action');
        });
    });

    // Marketing & Analytics
    Route::prefix('marketing')->name('marketing.')->group(function () {
        
        // Coupons
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('index');
            Route::get('/create', [CouponController::class, 'create'])->name('create');
            Route::post('/', [CouponController::class, 'store'])->name('store');
            Route::get('/{coupon}', [CouponController::class, 'show'])->name('show');
            Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
            Route::put('/{coupon}', [CouponController::class, 'update'])->name('update');
            Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');
            Route::post('/{coupon}/toggle', [CouponController::class, 'toggle'])->name('toggle');
            Route::get('/{coupon}/usage', [CouponController::class, 'usage'])->name('usage');
        });

        // Campaigns
        Route::prefix('campaigns')->name('campaigns.')->group(function () {
            Route::get('/', [MarketingController::class, 'index'])->name('index');
            Route::get('/create', [MarketingController::class, 'create'])->name('create');
            Route::post('/', [MarketingController::class, 'store'])->name('store');
            Route::get('/{campaign}', [MarketingController::class, 'show'])->name('show');
            Route::get('/{campaign}/edit', [MarketingController::class, 'edit'])->name('edit');
            Route::put('/{campaign}', [MarketingController::class, 'update'])->name('update');
            Route::delete('/{campaign}', [MarketingController::class, 'destroy'])->name('destroy');
            Route::post('/{campaign}/start', [MarketingController::class, 'start'])->name('start');
            Route::post('/{campaign}/pause', [MarketingController::class, 'pause'])->name('pause');
            Route::get('/{campaign}/analytics', [MarketingController::class, 'analytics'])->name('analytics');
        });
    });

    // Analytics & Reports
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/orders', [AnalyticsController::class, 'orders'])->name('orders');
        Route::get('/customers', [AnalyticsController::class, 'customers'])->name('customers');
        Route::get('/revenue', [AnalyticsController::class, 'revenue'])->name('revenue');
        Route::get('/website', [AnalyticsController::class, 'website'])->name('website');
        Route::get('/performance', [AnalyticsController::class, 'performance'])->name('performance');
        Route::get('/export/{type}', [AnalyticsController::class, 'export'])->name('export');
    });

    // Security & Monitoring
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/', [SecurityController::class, 'index'])->name('index');
        Route::get('/logs', [SecurityController::class, 'logs'])->name('logs');
        Route::get('/failed-logins', [SecurityController::class, 'failedLogins'])->name('failed-logins');
        Route::get('/suspicious-activity', [SecurityController::class, 'suspiciousActivity'])->name('suspicious-activity');
        Route::post('/block-ip', [SecurityController::class, 'blockIP'])->name('block-ip');
        Route::post('/unblock-ip', [SecurityController::class, 'unblockIP'])->name('unblock-ip');
        Route::get('/export-logs', [SecurityController::class, 'exportLogs'])->name('export-logs');
    });

    // Employee Management
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::get('/{employee}/assignments', [EmployeeController::class, 'assignments'])->name('assignments');
        Route::get('/{employee}/performance', [EmployeeController::class, 'performance'])->name('performance');
        Route::post('/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('toggle-status');
    });

    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/enhanced', [SettingsController::class, 'enhanced'])->name('enhanced');
        Route::post('/enhanced', [SettingsController::class, 'updateEnhanced'])->name('enhanced.update');
        Route::get('/notifications', [SettingsController::class, 'notifications'])->name('notifications');
        Route::post('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
        Route::get('/automation', [SettingsController::class, 'automation'])->name('automation');
        Route::post('/automation', [SettingsController::class, 'updateAutomation'])->name('automation.update');
    });

    // API Routes for AJAX calls
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/orders/search', [EnhancedOrderController::class, 'search'])->name('orders.search');
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/dashboard/widgets', [EnhancedDashboardController::class, 'widgets'])->name('dashboard.widgets');
        Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    });
});
