<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Orders Management
            ['name' => 'orders.view', 'display_name' => 'عرض الطلبات', 'module' => 'orders'],
            ['name' => 'orders.create', 'display_name' => 'إنشاء طلب', 'module' => 'orders'],
            ['name' => 'orders.edit', 'display_name' => 'تعديل الطلبات', 'module' => 'orders'],
            ['name' => 'orders.delete', 'display_name' => 'حذف الطلبات', 'module' => 'orders'],
            ['name' => 'orders.assign', 'display_name' => 'تعيين الطلبات', 'module' => 'orders'],
            ['name' => 'orders.status', 'display_name' => 'تغيير حالة الطلب', 'module' => 'orders'],
            ['name' => 'orders.export', 'display_name' => 'تصدير الطلبات', 'module' => 'orders'],

            // Financial Management
            ['name' => 'financial.view', 'display_name' => 'عرض المالية', 'module' => 'financial'],
            ['name' => 'financial.invoices.view', 'display_name' => 'عرض الفواتير', 'module' => 'financial'],
            ['name' => 'financial.invoices.create', 'display_name' => 'إنشاء فاتورة', 'module' => 'financial'],
            ['name' => 'financial.invoices.edit', 'display_name' => 'تعديل الفواتير', 'module' => 'financial'],
            ['name' => 'financial.invoices.delete', 'display_name' => 'حذف الفواتير', 'module' => 'financial'],
            ['name' => 'financial.payments.view', 'display_name' => 'عرض المدفوعات', 'module' => 'financial'],
            ['name' => 'financial.payments.create', 'display_name' => 'إضافة مدفوعات', 'module' => 'financial'],
            ['name' => 'financial.payments.edit', 'display_name' => 'تعديل المدفوعات', 'module' => 'financial'],
            ['name' => 'financial.reports', 'display_name' => 'التقارير المالية', 'module' => 'financial'],
            ['name' => 'financial.export', 'display_name' => 'تصدير البيانات المالية', 'module' => 'financial'],

            // CRM Management
            ['name' => 'crm.view', 'display_name' => 'عرض CRM', 'module' => 'crm'],
            ['name' => 'crm.leads.view', 'display_name' => 'عرض العملاء المحتملين', 'module' => 'crm'],
            ['name' => 'crm.leads.create', 'display_name' => 'إضافة عميل محتمل', 'module' => 'crm'],
            ['name' => 'crm.leads.edit', 'display_name' => 'تعديل العملاء المحتملين', 'module' => 'crm'],
            ['name' => 'crm.leads.delete', 'display_name' => 'حذف العملاء المحتملين', 'module' => 'crm'],
            ['name' => 'crm.leads.convert', 'display_name' => 'تحويل العملاء المحتملين', 'module' => 'crm'],
            ['name' => 'crm.quotes.view', 'display_name' => 'عرض العروض', 'module' => 'crm'],
            ['name' => 'crm.quotes.create', 'display_name' => 'إنشاء عرض', 'module' => 'crm'],
            ['name' => 'crm.quotes.edit', 'display_name' => 'تعديل العروض', 'module' => 'crm'],
            ['name' => 'crm.quotes.delete', 'display_name' => 'حذف العروض', 'module' => 'crm'],
            ['name' => 'crm.activities', 'display_name' => 'إدارة الأنشطة', 'module' => 'crm'],
            ['name' => 'crm.reports', 'display_name' => 'تقارير CRM', 'module' => 'crm'],

            // Marketing Management
            ['name' => 'marketing.view', 'display_name' => 'عرض التسويق', 'module' => 'marketing'],
            ['name' => 'marketing.campaigns.view', 'display_name' => 'عرض الحملات', 'module' => 'marketing'],
            ['name' => 'marketing.campaigns.create', 'display_name' => 'إنشاء حملة', 'module' => 'marketing'],
            ['name' => 'marketing.campaigns.edit', 'display_name' => 'تعديل الحملات', 'module' => 'marketing'],
            ['name' => 'marketing.campaigns.delete', 'display_name' => 'حذف الحملات', 'module' => 'marketing'],
            ['name' => 'marketing.coupons.view', 'display_name' => 'عرض الكوبونات', 'module' => 'marketing'],
            ['name' => 'marketing.coupons.create', 'display_name' => 'إنشاء كوبون', 'module' => 'marketing'],
            ['name' => 'marketing.coupons.edit', 'display_name' => 'تعديل الكوبونات', 'module' => 'marketing'],
            ['name' => 'marketing.coupons.delete', 'display_name' => 'حذف الكوبونات', 'module' => 'marketing'],
            ['name' => 'marketing.analytics', 'display_name' => 'تحليلات التسويق', 'module' => 'marketing'],

            // Analytics & Reports
            ['name' => 'analytics.view', 'display_name' => 'عرض التحليلات', 'module' => 'analytics'],
            ['name' => 'analytics.sales', 'display_name' => 'تحليلات المبيعات', 'module' => 'analytics'],
            ['name' => 'analytics.customers', 'display_name' => 'تحليلات العملاء', 'module' => 'analytics'],
            ['name' => 'analytics.performance', 'display_name' => 'تحليلات الأداء', 'module' => 'analytics'],
            ['name' => 'analytics.export', 'display_name' => 'تصدير التقارير', 'module' => 'analytics'],

            // Employee Management
            ['name' => 'employees.view', 'display_name' => 'عرض الموظفين', 'module' => 'employees'],
            ['name' => 'employees.create', 'display_name' => 'إضافة موظف', 'module' => 'employees'],
            ['name' => 'employees.edit', 'display_name' => 'تعديل الموظفين', 'module' => 'employees'],
            ['name' => 'employees.delete', 'display_name' => 'حذف الموظفين', 'module' => 'employees'],
            ['name' => 'employees.roles', 'display_name' => 'إدارة الأدوار', 'module' => 'employees'],
            ['name' => 'employees.permissions', 'display_name' => 'إدارة الصلاحيات', 'module' => 'employees'],
            ['name' => 'employees.performance', 'display_name' => 'تقييم الأداء', 'module' => 'employees'],

            // Security Management
            ['name' => 'security.view', 'display_name' => 'عرض الأمان', 'module' => 'security'],
            ['name' => 'security.logs', 'display_name' => 'سجلات الأمان', 'module' => 'security'],
            ['name' => 'security.settings', 'display_name' => 'إعدادات الأمان', 'module' => 'security'],
            ['name' => 'security.block', 'display_name' => 'حظر المستخدمين', 'module' => 'security'],
            ['name' => 'security.unblock', 'display_name' => 'إلغاء الحظر', 'module' => 'security'],

            // System Settings
            ['name' => 'settings.view', 'display_name' => 'عرض الإعدادات', 'module' => 'settings'],
            ['name' => 'settings.edit', 'display_name' => 'تعديل الإعدادات', 'module' => 'settings'],
            ['name' => 'settings.system', 'display_name' => 'إعدادات النظام', 'module' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}

