<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EnhancedOrderController;
use App\Http\Controllers\Admin\EnhancedDashboardController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CRMController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\QuestionnaireController;

/*
|--------------------------------------------------------------------------
| Enhanced Admin Routes
|--------------------------------------------------------------------------
|
| Here are all the enhanced admin routes for the SOFA system.
| These routes are protected by authentication and permission middleware.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'security', 'audit'])->group(function () {
    
    // Enhanced Dashboard
    Route::get('/dashboard/enhanced', [EnhancedDashboardController::class, 'index'])->name('dashboard.enhanced');
    Route::get('/dashboard/stats', [EnhancedDashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/widgets', [EnhancedDashboardController::class, 'getWidgets'])->name('dashboard.widgets');
    
    // Enhanced Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/enhanced', [EnhancedOrderController::class, 'index'])->name('enhanced.index');
        Route::get('/enhanced/create', [EnhancedOrderController::class, 'create'])->name('enhanced.create');
        Route::post('/enhanced', [EnhancedOrderController::class, 'store'])->name('enhanced.store');
        Route::get('/enhanced/{order}', [EnhancedOrderController::class, 'show'])->name('enhanced.show');
        Route::get('/enhanced/{order}/edit', [EnhancedOrderController::class, 'edit'])->name('enhanced.edit');
        Route::put('/enhanced/{order}', [EnhancedOrderController::class, 'update'])->name('enhanced.update');
        Route::delete('/enhanced/{order}', [EnhancedOrderController::class, 'destroy'])->name('enhanced.destroy');
        
        // Order Status Management
        Route::post('/{order}/status', [EnhancedOrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/timeline', [EnhancedOrderController::class, 'addTimelineEvent'])->name('add-timeline');
        Route::post('/{order}/assign', [EnhancedOrderController::class, 'assignEmployee'])->name('assign-employee');
        Route::delete('/{order}/assign/{assignment}', [EnhancedOrderController::class, 'removeAssignment'])->name('remove-assignment');
        
        // Order Actions
        Route::post('/{order}/duplicate', [EnhancedOrderController::class, 'duplicate'])->name('duplicate');
        Route::post('/{order}/archive', [EnhancedOrderController::class, 'archive'])->name('archive');
        Route::post('/{order}/restore', [EnhancedOrderController::class, 'restore'])->name('restore');
        
        // Bulk Operations
        Route::post('/bulk/status', [EnhancedOrderController::class, 'bulkUpdateStatus'])->name('bulk.status');
        Route::post('/bulk/assign', [EnhancedOrderController::class, 'bulkAssign'])->name('bulk.assign');
        Route::post('/bulk/export', [EnhancedOrderController::class, 'bulkExport'])->name('bulk.export');
        Route::delete('/bulk/delete', [EnhancedOrderController::class, 'bulkDelete'])->name('bulk.delete');
        
        // Import/Export
        Route::get('/export', [EnhancedOrderController::class, 'export'])->name('export');
        Route::get('/import', [EnhancedOrderController::class, 'showImport'])->name('import.show');
        Route::post('/import', [EnhancedOrderController::class, 'import'])->name('import');
        Route::get('/template', [EnhancedOrderController::class, 'downloadTemplate'])->name('template');
        
        // Reports
        Route::get('/reports', [EnhancedOrderController::class, 'reports'])->name('reports');
        Route::get('/reports/performance', [EnhancedOrderController::class, 'performanceReport'])->name('reports.performance');
        Route::get('/reports/timeline', [EnhancedOrderController::class, 'timelineReport'])->name('reports.timeline');
        
        // API Endpoints
        Route::get('/api/search', [EnhancedOrderController::class, 'search'])->name('api.search');
        Route::get('/api/stats', [EnhancedOrderController::class, 'getStats'])->name('api.stats');
        Route::get('/api/timeline/{order}', [EnhancedOrderController::class, 'getTimeline'])->name('api.timeline');
    });
    
    // Analytics & Reports
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/orders', [AnalyticsController::class, 'orders'])->name('orders');
        Route::get('/sales', [AnalyticsController::class, 'sales'])->name('sales');
        Route::get('/customers', [AnalyticsController::class, 'customers'])->name('customers');
        Route::get('/performance', [AnalyticsController::class, 'performance'])->name('performance');
        Route::get('/trends', [AnalyticsController::class, 'trends'])->name('trends');
        Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
        
        // API Endpoints
        Route::get('/api/dashboard', [AnalyticsController::class, 'getDashboardData'])->name('api.dashboard');
        Route::get('/api/charts', [AnalyticsController::class, 'getChartData'])->name('api.charts');
        Route::get('/api/kpis', [AnalyticsController::class, 'getKPIs'])->name('api.kpis');
    });
    
    // Financial Management
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [FinancialController::class, 'index'])->name('index');
        
        // Invoices
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [FinancialController::class, 'invoices'])->name('index');
            Route::get('/create', [FinancialController::class, 'createInvoice'])->name('create');
            Route::post('/', [FinancialController::class, 'storeInvoice'])->name('store');
            Route::get('/{invoice}', [FinancialController::class, 'showInvoice'])->name('show');
            Route::get('/{invoice}/edit', [FinancialController::class, 'editInvoice'])->name('edit');
            Route::put('/{invoice}', [FinancialController::class, 'updateInvoice'])->name('update');
            Route::delete('/{invoice}', [FinancialController::class, 'destroyInvoice'])->name('destroy');
            Route::get('/{invoice}/pdf', [FinancialController::class, 'downloadInvoicePDF'])->name('pdf');
            Route::post('/{invoice}/send', [FinancialController::class, 'sendInvoice'])->name('send');
            Route::post('/{invoice}/mark-paid', [FinancialController::class, 'markAsPaid'])->name('mark-paid');
        });
        
        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [FinancialController::class, 'payments'])->name('index');
            Route::get('/create', [FinancialController::class, 'createPayment'])->name('create');
            Route::post('/', [FinancialController::class, 'storePayment'])->name('store');
            Route::get('/{payment}', [FinancialController::class, 'showPayment'])->name('show');
            Route::get('/{payment}/edit', [FinancialController::class, 'editPayment'])->name('edit');
            Route::put('/{payment}', [FinancialController::class, 'updatePayment'])->name('update');
            Route::delete('/{payment}', [FinancialController::class, 'destroyPayment'])->name('destroy');
            Route::post('/{payment}/refund', [FinancialController::class, 'refundPayment'])->name('refund');
        });
        
        // Payment Methods
        Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/', [FinancialController::class, 'paymentMethods'])->name('index');
            Route::get('/create', [FinancialController::class, 'createPaymentMethod'])->name('create');
            Route::post('/', [FinancialController::class, 'storePaymentMethod'])->name('store');
            Route::get('/{paymentMethod}/edit', [FinancialController::class, 'editPaymentMethod'])->name('edit');
            Route::put('/{paymentMethod}', [FinancialController::class, 'updatePaymentMethod'])->name('update');
            Route::delete('/{paymentMethod}', [FinancialController::class, 'destroyPaymentMethod'])->name('destroy');
            Route::post('/{paymentMethod}/toggle', [FinancialController::class, 'togglePaymentMethod'])->name('toggle');
        });
        
        // Reports
        Route::get('/reports', [FinancialController::class, 'reports'])->name('reports');
        Route::get('/reports/revenue', [FinancialController::class, 'revenueReport'])->name('reports.revenue');
        Route::get('/reports/profit', [FinancialController::class, 'profitReport'])->name('reports.profit');
        Route::get('/reports/tax', [FinancialController::class, 'taxReport'])->name('reports.tax');
        Route::get('/export', [FinancialController::class, 'export'])->name('export');
    });
    
    // Employee Management
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{user}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{user}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{user}', [EmployeeController::class, 'destroy'])->name('destroy');
        
        // Employee Actions
        Route::post('/{user}/activate', [EmployeeController::class, 'activate'])->name('activate');
        Route::post('/{user}/deactivate', [EmployeeController::class, 'deactivate'])->name('deactivate');
        Route::post('/{user}/reset-password', [EmployeeController::class, 'resetPassword'])->name('reset-password');
        Route::post('/{user}/send-welcome', [EmployeeController::class, 'sendWelcomeEmail'])->name('send-welcome');
        
        // Performance & Reports
        Route::get('/{user}/performance', [EmployeeController::class, 'performance'])->name('performance');
        Route::get('/{user}/activity', [EmployeeController::class, 'activity'])->name('activity');
        Route::get('/reports/performance', [EmployeeController::class, 'performanceReport'])->name('reports.performance');
        Route::get('/export', [EmployeeController::class, 'export'])->name('export');
    });
    
    // Permissions & Roles
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/roles', [PermissionController::class, 'roles'])->name('roles');
        Route::get('/roles/create', [PermissionController::class, 'createRole'])->name('roles.create');
        Route::post('/roles', [PermissionController::class, 'storeRole'])->name('roles.store');
        Route::get('/roles/{role}/edit', [PermissionController::class, 'editRole'])->name('roles.edit');
        Route::put('/roles/{role}', [PermissionController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [PermissionController::class, 'destroyRole'])->name('roles.destroy');
        
        Route::get('/permissions', [PermissionController::class, 'permissions'])->name('permissions');
        Route::post('/assign', [PermissionController::class, 'assignPermissions'])->name('assign');
        Route::post('/revoke', [PermissionController::class, 'revokePermissions'])->name('revoke');
        Route::get('/matrix', [PermissionController::class, 'permissionMatrix'])->name('matrix');
    });
    
    // CRM System
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::get('/', [CRMController::class, 'index'])->name('index');
        
        // Leads Management
        Route::prefix('leads')->name('leads.')->group(function () {
            Route::get('/', [CRMController::class, 'leads'])->name('index');
            Route::get('/create', [CRMController::class, 'createLead'])->name('create');
            Route::post('/', [CRMController::class, 'storeLead'])->name('store');
            Route::get('/{lead}', [CRMController::class, 'showLead'])->name('show');
            Route::get('/{lead}/edit', [CRMController::class, 'editLead'])->name('edit');
            Route::put('/{lead}', [CRMController::class, 'updateLead'])->name('update');
            Route::delete('/{lead}', [CRMController::class, 'destroyLead'])->name('destroy');
            Route::post('/{lead}/convert', [CRMController::class, 'convertLead'])->name('convert');
            Route::post('/{lead}/assign', [CRMController::class, 'assignLead'])->name('assign');
        });
        
        // Quotes Management
        Route::prefix('quotes')->name('quotes.')->group(function () {
            Route::get('/', [CRMController::class, 'quotes'])->name('index');
            Route::get('/create', [CRMController::class, 'createQuote'])->name('create');
            Route::post('/', [CRMController::class, 'storeQuote'])->name('store');
            Route::get('/{quote}', [CRMController::class, 'showQuote'])->name('show');
            Route::get('/{quote}/edit', [CRMController::class, 'editQuote'])->name('edit');
            Route::put('/{quote}', [CRMController::class, 'updateQuote'])->name('update');
            Route::delete('/{quote}', [CRMController::class, 'destroyQuote'])->name('destroy');
            Route::get('/{quote}/pdf', [CRMController::class, 'downloadQuotePDF'])->name('pdf');
            Route::post('/{quote}/send', [CRMController::class, 'sendQuote'])->name('send');
            Route::post('/{quote}/accept', [CRMController::class, 'acceptQuote'])->name('accept');
            Route::post('/{quote}/reject', [CRMController::class, 'rejectQuote'])->name('reject');
        });
        
        // Activities & Follow-ups
        Route::prefix('activities')->name('activities.')->group(function () {
            Route::get('/', [CRMController::class, 'activities'])->name('index');
            Route::post('/', [CRMController::class, 'storeActivity'])->name('store');
            Route::put('/{activity}', [CRMController::class, 'updateActivity'])->name('update');
            Route::delete('/{activity}', [CRMController::class, 'destroyActivity'])->name('destroy');
        });
        
        // Reports
        Route::get('/reports', [CRMController::class, 'reports'])->name('reports');
        Route::get('/reports/conversion', [CRMController::class, 'conversionReport'])->name('reports.conversion');
        Route::get('/reports/pipeline', [CRMController::class, 'pipelineReport'])->name('reports.pipeline');
    });
    
    // Marketing & Campaigns
    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/', [MarketingController::class, 'enhancedDashboard'])->name('enhanced-dashboard');
        Route::get('/analytics', [MarketingController::class, 'analytics'])->name('analytics');
        
        // Coupons Management
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [MarketingController::class, 'coupons'])->name('index');
            Route::get('/create', [MarketingController::class, 'createCoupon'])->name('create');
            Route::post('/', [MarketingController::class, 'storeCoupon'])->name('store');
            Route::get('/{coupon}/edit', [MarketingController::class, 'editCoupon'])->name('edit');
            Route::put('/{coupon}', [MarketingController::class, 'updateCoupon'])->name('update');
            Route::delete('/{coupon}', [MarketingController::class, 'destroyCoupon'])->name('destroy');
            Route::post('/promotional-campaign', [MarketingController::class, 'createPromotionalCampaign'])->name('promotional-campaign');
        });
        
        // Campaigns Management
        Route::prefix('campaigns')->name('campaigns.')->group(function () {
            Route::get('/', [MarketingController::class, 'campaigns'])->name('index');
            Route::get('/create', [MarketingController::class, 'createCampaign'])->name('create');
            Route::post('/', [MarketingController::class, 'storeCampaign'])->name('store');
            Route::get('/{campaign}', [MarketingController::class, 'showCampaign'])->name('show');
            Route::get('/{campaign}/edit', [MarketingController::class, 'editCampaign'])->name('edit');
            Route::put('/{campaign}', [MarketingController::class, 'updateCampaign'])->name('update');
            Route::delete('/{campaign}', [MarketingController::class, 'destroyCampaign'])->name('destroy');
            Route::post('/{campaign}/start', [MarketingController::class, 'startCampaign'])->name('start');
            Route::post('/{campaign}/pause', [MarketingController::class, 'pauseCampaign'])->name('pause');
            Route::post('/{campaign}/resume', [MarketingController::class, 'resumeCampaign'])->name('resume');
            Route::post('/{campaign}/complete', [MarketingController::class, 'completeCampaign'])->name('complete');
        });
        
        // Cleanup & Maintenance
        Route::post('/cleanup', [MarketingController::class, 'cleanup'])->name('cleanup');
    });
    
    // Security Management
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/', [SecurityController::class, 'index'])->name('index');
        Route::get('/logs', [SecurityController::class, 'logs'])->name('logs');
        Route::get('/failed-logins', [SecurityController::class, 'failedLogins'])->name('failed-logins');
        Route::get('/analytics', [SecurityController::class, 'analytics'])->name('analytics');
        Route::get('/settings', [SecurityController::class, 'settings'])->name('settings');
        Route::put('/settings', [SecurityController::class, 'updateSettings'])->name('settings.update');
        
        // Security Actions
        Route::post('/block-ip', [SecurityController::class, 'blockIp'])->name('block-ip');
        Route::post('/unblock-ip', [SecurityController::class, 'unblockIp'])->name('unblock-ip');
        Route::post('/block-email', [SecurityController::class, 'blockEmail'])->name('block-email');
        Route::post('/unblock-email', [SecurityController::class, 'unblockEmail'])->name('unblock-email');
        Route::post('/block-user', [SecurityController::class, 'blockUser'])->name('block-user');
        Route::post('/unblock-user', [SecurityController::class, 'unblockUser'])->name('unblock-user');
        Route::post('/force-password-reset', [SecurityController::class, 'forcePasswordReset'])->name('force-password-reset');
        
        // Log Actions
        Route::post('/logs/{log}/mark-reviewed', [SecurityController::class, 'markAsReviewed'])->name('logs.mark-reviewed');
        Route::post('/logs/{log}/mark-suspicious', [SecurityController::class, 'markAsSuspicious'])->name('logs.mark-suspicious');
        
        // Maintenance
        Route::post('/cleanup', [SecurityController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [SecurityController::class, 'exportReport'])->name('export');
        
        // API Endpoints
        Route::get('/api/stats', [SecurityController::class, 'getStats'])->name('api.stats');
        Route::get('/api/alerts', [SecurityController::class, 'getAlerts'])->name('api.alerts');
    });
    
    // Notifications Management
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/templates', [NotificationController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [NotificationController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [NotificationController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/templates/{template}/edit', [NotificationController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{template}', [NotificationController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{template}', [NotificationController::class, 'destroyTemplate'])->name('templates.destroy');
        
        Route::get('/send', [NotificationController::class, 'send'])->name('send');
        Route::post('/send', [NotificationController::class, 'sendNotification'])->name('send.store');
        Route::get('/history', [NotificationController::class, 'history'])->name('history');
        Route::get('/settings', [NotificationController::class, 'settings'])->name('settings');
        Route::put('/settings', [NotificationController::class, 'updateSettings'])->name('settings.update');
    });
    
    // Gallery Management
    Route::prefix('gallery')->name('gallery.')->group(function () {
        Route::get('/', [GalleryController::class, 'index'])->name('index');
        Route::get('/categories', [GalleryController::class, 'categories'])->name('categories');
        Route::get('/categories/create', [GalleryController::class, 'createCategory'])->name('categories.create');
        Route::post('/categories', [GalleryController::class, 'storeCategory'])->name('categories.store');
        Route::get('/categories/{category}/edit', [GalleryController::class, 'editCategory'])->name('categories.edit');
        Route::put('/categories/{category}', [GalleryController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [GalleryController::class, 'destroyCategory'])->name('categories.destroy');
        
        Route::get('/items', [GalleryController::class, 'items'])->name('items');
        Route::get('/items/create', [GalleryController::class, 'createItem'])->name('items.create');
        Route::post('/items', [GalleryController::class, 'storeItem'])->name('items.store');
        Route::get('/items/{item}/edit', [GalleryController::class, 'editItem'])->name('items.edit');
        Route::put('/items/{item}', [GalleryController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{item}', [GalleryController::class, 'destroyItem'])->name('items.destroy');
        
        Route::post('/items/bulk-upload', [GalleryController::class, 'bulkUpload'])->name('items.bulk-upload');
        Route::post('/items/bulk-delete', [GalleryController::class, 'bulkDelete'])->name('items.bulk-delete');
    });
    
    // Questionnaire Management
    Route::prefix('questionnaires')->name('questionnaires.')->group(function () {
        Route::get('/', [QuestionnaireController::class, 'index'])->name('index');
        Route::get('/create', [QuestionnaireController::class, 'create'])->name('create');
        Route::post('/', [QuestionnaireController::class, 'store'])->name('store');
        Route::get('/{questionnaire}', [QuestionnaireController::class, 'show'])->name('show');
        Route::get('/{questionnaire}/edit', [QuestionnaireController::class, 'edit'])->name('edit');
        Route::put('/{questionnaire}', [QuestionnaireController::class, 'update'])->name('update');
        Route::delete('/{questionnaire}', [QuestionnaireController::class, 'destroy'])->name('destroy');
        
        Route::get('/responses', [QuestionnaireController::class, 'responses'])->name('responses');
        Route::get('/responses/{response}', [QuestionnaireController::class, 'showResponse'])->name('responses.show');
        Route::delete('/responses/{response}', [QuestionnaireController::class, 'destroyResponse'])->name('responses.destroy');
        Route::get('/export', [QuestionnaireController::class, 'export'])->name('export');
        
        Route::post('/{questionnaire}/toggle', [QuestionnaireController::class, 'toggle'])->name('toggle');
        Route::post('/{questionnaire}/duplicate', [QuestionnaireController::class, 'duplicate'])->name('duplicate');
    });
    
    // System Maintenance & Tools
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/maintenance', function () {
            return view('admin.system.maintenance');
        })->name('maintenance');
        
        Route::get('/backup', function () {
            return view('admin.system.backup');
        })->name('backup');
        
        Route::get('/logs', function () {
            return view('admin.system.logs');
        })->name('logs');
        
        Route::get('/cache', function () {
            return view('admin.system.cache');
        })->name('cache');
        
        Route::post('/cache/clear', function () {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
            return back()->with('success', 'تم مسح الكاش بنجاح');
        })->name('cache.clear');
    });
});

// API Routes for AJAX calls
Route::prefix('api/admin')->name('api.admin.')->middleware(['auth', 'security'])->group(function () {
    Route::get('/dashboard/stats', [EnhancedDashboardController::class, 'getStats']);
    Route::get('/orders/search', [EnhancedOrderController::class, 'search']);
    Route::get('/analytics/charts', [AnalyticsController::class, 'getChartData']);
    Route::get('/security/alerts', [SecurityController::class, 'getAlerts']);
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread']);
});
