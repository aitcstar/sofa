<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin - All Permissions
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'مدير النظام',
                'description' => 'صلاحيات كاملة على جميع أقسام النظام',
                'is_active' => true,
            ]
        );
        $superAdmin->syncPermissions(Permission::all());

        // Orders Manager
        $ordersManager = Role::firstOrCreate(
            ['name' => 'orders_manager'],
            [
                'display_name' => 'مدير الطلبات',
                'description' => 'إدارة كاملة للطلبات والتعيينات',
                'is_active' => true,
            ]
        );
        $ordersManager->syncPermissions(Permission::where('module', 'orders')->pluck('name')->toArray());

        // Financial Manager
        $financialManager = Role::firstOrCreate(
            ['name' => 'financial_manager'],
            [
                'display_name' => 'المدير المالي',
                'description' => 'إدارة كاملة للشؤون المالية والفواتير',
                'is_active' => true,
            ]
        );
        $financialManager->syncPermissions(Permission::where('module', 'financial')->pluck('name')->toArray());

        // Sales Manager
        $salesManager = Role::firstOrCreate(
            ['name' => 'sales_manager'],
            [
                'display_name' => 'مدير المبيعات',
                'description' => 'إدارة العملاء المحتملين والعروض',
                'is_active' => true,
            ]
        );
        $salesManager->syncPermissions(Permission::whereIn('module', ['crm', 'orders'])->pluck('name')->toArray());

        // Marketing Manager
        $marketingManager = Role::firstOrCreate(
            ['name' => 'marketing_manager'],
            [
                'display_name' => 'مدير التسويق',
                'description' => 'إدارة الحملات التسويقية والكوبونات',
                'is_active' => true,
            ]
        );
        $marketingManager->syncPermissions(Permission::where('module', 'marketing')->pluck('name')->toArray());

        // Employee (Basic)
        $employee = Role::firstOrCreate(
            ['name' => 'employee'],
            [
                'display_name' => 'موظف',
                'description' => 'صلاحيات أساسية للموظف',
                'is_active' => true,
            ]
        );
        $employee->syncPermissions([
            'orders.view',
            'orders.status',
            'crm.leads.view',
            'crm.activities',
        ]);

        // Customer Service
        $customerService = Role::firstOrCreate(
            ['name' => 'customer_service'],
            [
                'display_name' => 'خدمة العملاء',
                'description' => 'التعامل مع الطلبات والعملاء',
                'is_active' => true,
            ]
        );
        $customerService->syncPermissions([
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.status',
            'crm.leads.view',
            'crm.leads.create',
            'crm.leads.edit',
            'crm.activities',
            'crm.quotes.view',
            'crm.quotes.create',
        ]);

        // Accountant
        $accountant = Role::firstOrCreate(
            ['name' => 'accountant'],
            [
                'display_name' => 'محاسب',
                'description' => 'إدارة الفواتير والمدفوعات',
                'is_active' => true,
            ]
        );
        $accountant->syncPermissions([
            'financial.view',
            'financial.invoices.view',
            'financial.invoices.create',
            'financial.invoices.edit',
            'financial.payments.view',
            'financial.payments.create',
            'financial.reports',
            'orders.view',
        ]);

        // Analyst
        $analyst = Role::firstOrCreate(
            ['name' => 'analyst'],
            [
                'display_name' => 'محلل بيانات',
                'description' => 'عرض التحليلات والتقارير',
                'is_active' => true,
            ]
        );
        $analyst->syncPermissions(Permission::where('module', 'analytics')->pluck('name')->toArray());
    }
}

