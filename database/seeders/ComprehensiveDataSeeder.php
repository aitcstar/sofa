<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        //$this->seedUsers();
        //$this->seedRolesAndPermissions();
        //$this->seedEmployees();
        //$this->seedCustomers();
       // $this->seedLeads();
        //$this->seedOrders();
        //$this->seedInvoices();
        //$this->seedPayments();
       // $this->seedCoupons();
        //$this->seedMarketingCampaigns();
        $this->seedWebsiteAnalytics();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ تم إنشاء جميع البيانات التجريبية بنجاح!');
    }

    /**
     * Seed users.
     */
    private function seedUsers()
    {
        $this->command->info('🔄 جاري إنشاء المستخدمين...');

        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0501234567',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Employee User',
                'email' => 'employee@example.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'phone' => '0501234568',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ تم إنشاء المستخدمين');
    }

    /**
     * Seed roles and permissions.
     */
    private function seedRolesAndPermissions()
    {
        $this->command->info('🔄 جاري إنشاء الأدوار والصلاحيات...');

        // Permissions
        $permissions = [
            ['name' => 'view_orders', 'display_name' => 'عرض الطلبات', 'group' => 'orders'],
            ['name' => 'create_orders', 'display_name' => 'إنشاء طلبات', 'group' => 'orders'],
            ['name' => 'edit_orders', 'display_name' => 'تعديل طلبات', 'group' => 'orders'],
            ['name' => 'delete_orders', 'display_name' => 'حذف طلبات', 'group' => 'orders'],

            ['name' => 'view_invoices', 'display_name' => 'عرض الفواتير', 'group' => 'financial'],
            ['name' => 'create_invoices', 'display_name' => 'إنشاء فواتير', 'group' => 'financial'],
            ['name' => 'edit_invoices', 'display_name' => 'تعديل فواتير', 'group' => 'financial'],
            ['name' => 'delete_invoices', 'display_name' => 'حذف فواتير', 'group' => 'financial'],

            ['name' => 'view_leads', 'display_name' => 'عرض العملاء المحتملين', 'group' => 'crm'],
            ['name' => 'create_leads', 'display_name' => 'إنشاء عملاء محتملين', 'group' => 'crm'],
            ['name' => 'edit_leads', 'display_name' => 'تعديل عملاء محتملين', 'group' => 'crm'],
            ['name' => 'delete_leads', 'display_name' => 'حذف عملاء محتملين', 'group' => 'crm'],

            ['name' => 'view_employees', 'display_name' => 'عرض الموظفين', 'group' => 'employees'],
            ['name' => 'create_employees', 'display_name' => 'إنشاء موظفين', 'group' => 'employees'],
            ['name' => 'edit_employees', 'display_name' => 'تعديل موظفين', 'group' => 'employees'],
            ['name' => 'delete_employees', 'display_name' => 'حذف موظفين', 'group' => 'employees'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'مدير النظام',
                'description' => 'صلاحيات كاملة على النظام',
                'is_active' => true,
            ],
            [
                'name' => 'manager',
                'display_name' => 'مدير',
                'description' => 'صلاحيات إدارية',
                'is_active' => true,
            ],
            [
                'name' => 'sales',
                'display_name' => 'موظف مبيعات',
                'description' => 'صلاحيات المبيعات',
                'is_active' => true,
            ],
            [
                'name' => 'support',
                'display_name' => 'موظف دعم',
                'description' => 'صلاحيات الدعم الفني',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ تم إنشاء الأدوار والصلاحيات');
    }

    /**
     * Seed employees.
     */
    private function seedEmployees()
    {
        $this->command->info('🔄 جاري إنشاء الموظفين...');

        $employees = [
            [
                'name' => 'أحمد محمد',
                'email' => 'ahmed@example.com',
                'phone' => '0501111111',
                'password' => Hash::make('password'),
                'job_title' => 'مدير المبيعات',
                'role_id' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'فاطمة علي',
                'email' => 'fatima@example.com',
                'phone' => '0502222222',
                'password' => Hash::make('password'),
                'job_title' => 'موظفة مبيعات',
                'role_id' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'خالد سعيد',
                'email' => 'khaled@example.com',
                'phone' => '0503333333',
                'password' => Hash::make('password'),
                'job_title' => 'موظف دعم فني',
                'role_id' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($employees as $employee) {
            DB::table('employees')->insert(array_merge($employee, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ تم إنشاء الموظفين');
    }

    /**
     * Seed customers.
     */
    private function seedCustomers()
    {
        $this->command->info('🔄 جاري إنشاء العملاء...');

        $customers = [
            ['name' => 'محمد عبدالله', 'email' => 'customer1@example.com', 'phone' => '0504444444'],
            ['name' => 'سارة أحمد', 'email' => 'customer2@example.com', 'phone' => '0505555555'],
            ['name' => 'عبدالرحمن خالد', 'email' => 'customer3@example.com', 'phone' => '0506666666'],
            ['name' => 'نورة سعد', 'email' => 'customer4@example.com', 'phone' => '0507777777'],
            ['name' => 'يوسف محمود', 'email' => 'customer5@example.com', 'phone' => '0508888888'],
        ];

        foreach ($customers as $customer) {
            DB::table('users')->insert(array_merge($customer, [
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ تم إنشاء العملاء');
    }

    /**
     * Seed leads.
     */
    private function seedLeads()
    {
        $this->command->info('🔄 جاري إنشاء العملاء المحتملين...');

        $leads = [
            [
                'name' => 'عبدالعزيز محمد',
                'email' => 'lead1@example.com',
                'phone' => '0509999999',
                'company' => 'شركة التطوير',
                'source' => 'website',
                'status' => 'new',
                'project_type' => 'تأثيث مكتب',
                'budget' => 50000,
                'assigned_to' => 1,
            ],
            [
                'name' => 'ريم عبدالله',
                'email' => 'lead2@example.com',
                'phone' => '0501010101',
                'company' => 'مؤسسة البناء',
                'source' => 'phone',
                'status' => 'contacted',
                'project_type' => 'تأثيث فيلا',
                'budget' => 150000,
                'assigned_to' => 1,
            ],
            [
                'name' => 'فهد سعود',
                'email' => 'lead3@example.com',
                'phone' => '0502020202',
                'company' => null,
                'source' => 'social_media',
                'status' => 'qualified',
                'project_type' => 'تأثيث شقة',
                'budget' => 80000,
                'assigned_to' => 2,
            ],
        ];

        foreach ($leads as $lead) {
            DB::table('leads')->insert(array_merge($lead, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ تم إنشاء العملاء المحتملين');
    }

    /**
     * Seed orders.
     */
    private function seedOrders()
    {
        $this->command->info('🔄 جاري إنشاء الطلبات...');

        $statuses = ['pending','confirmed','processing','shipped','delivered','archived','cancelled'];
        $paymentStatuses = ['unpaid', 'partial', 'paid', 'refunded'];

        for ($i = 1; $i <= 20; $i++) {
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $totalAmount = rand(10000, 200000);
            $paidAmount = $paymentStatus === 'paid' ? $totalAmount : ($paymentStatus === 'partial' ? $totalAmount * 0.5 : 0);

            DB::table('orders')->insert([
                'user_id' => rand(3, 7), // Customer IDs
                'package_id' => rand(1, 4),
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'name' => 'عميل رقم ' . $i,
                'email' => 'customer' . $i . '@example.com',
                'country_code' => '050' ,
                'phone' => rand(1000000, 9999999),
                'project_type' => ['large', 'medium', 'small'][array_rand(['large', 'medium', 'small'])],
                'status' => $status,
                'base_amount' => $totalAmount,
                'payment_status' => $paymentStatus,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'assigned_to' => rand(1, 3),
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('✅ تم إنشاء الطلبات');
    }

    /**
     * Seed invoices.
     */
    private function seedInvoices()
    {
        $this->command->info('🔄 جاري إنشاء الفواتير...');

        $orders = DB::table('orders')->get();

        foreach ($orders as $index => $order) {
            $baseAmount = $order->total_amount * 0.87; // Before tax
            $taxAmount = $baseAmount * 0.15;
            $totalAmount = $baseAmount + $taxAmount;

            DB::table('invoices')->insert([
                'order_id' => $order->id,
                'customer_id' => rand(3, 7),
                //'user_id' => rand(3, 7),
                'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'issue_date' => Carbon::parse($order->created_at),
                'due_date' => Carbon::parse($order->created_at)->addDays(30),
                'base_amount' => $baseAmount,
                'subtotal' => $baseAmount,
                'tax_rate' => 15,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'paid_amount' => $order->paid_amount,
                //'status' => $order->payment_status === 'paid' ? 'paid' : ($order->payment_status === 'partial' ? 'partial' : 'unpaid'),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);
        }

        $this->command->info('✅ تم إنشاء الفواتير');
    }

    /**
     * Seed payments.
     */
    private function seedPayments()
    {
        $this->command->info('🔄 جاري إنشاء المدفوعات...');

        $invoices = DB::table('invoices')->where('paid_amount', '>', 0)->get();
        //$orders = DB::table('orders')->get();

        foreach ($invoices as $invoice) {
            DB::table('payments')->insert([
                'order_id' => rand(277, 296),
                'invoice_id' => $invoice->id,
                'customer_id' => rand(3, 7),
                'amount' => $invoice->paid_amount,
                'payment_method' => ['cash', 'bank_transfer', 'credit_card'][array_rand(['cash', 'bank_transfer', 'credit_card'])],
                'payment_date' => Carbon::parse($invoice->created_at)->addDays(rand(1, 15)),
                'payment_number' => 'PAY-' . rand(100000, 999999),
                'reference_number' => 'PAY-' . rand(100000, 999999),
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ تم إنشاء المدفوعات');
    }

    /**
     * Seed coupons.
     */
    private function seedCoupons()
    {
        $this->command->info('🔄 جاري إنشاء الكوبونات...');

        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10,
                'description' => 'خصم 10% للعملاء الجدد',
                'max_uses' => 100,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'SUMMER2024',
                'type' => 'fixed_amount',
                'value' => 5000,
                'description' => 'خصم 5000 ريال - عرض الصيف',
                'max_uses' => 50,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'VIP20',
                'type' => 'percentage',//'percentage','fixed_amount'
                'value' => 20,
                'description' => 'خصم 20% للعملاء المميزين',
                'max_uses' => 20,
                'valid_from' => now(),
                'valid_until' => now()->addYear(),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            DB::table('coupons')->insert(array_merge($coupon, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ تم إنشاء الكوبونات');
    }

    /**
     * Seed marketing campaigns.
     */
    private function seedMarketingCampaigns()
    {
        $this->command->info('🔄 جاري إنشاء الحملات التسويقية...');

        $campaigns = [
            [
                'name' => 'حملة العروض الصيفية',
                'type' => 'email',
                'description' => 'حملة بريد إلكتروني للعروض الصيفية',
                'start_date' => now(),
                'end_date' => now()->addMonths(2),
                'budget' => 10000,
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'name' => 'حملة سوشيال ميديا',
                'type' => 'social',
                'description' => 'حملة على منصات التواصل الاجتماعي',
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(30),
                'budget' => 15000,
                'status' => 'active',
                'created_by' => 1,
            ],
        ];

        foreach ($campaigns as $campaign) {
            DB::table('marketing_campaigns')->insert(array_merge($campaign, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ تم إنشاء الحملات التسويقية');
    }

    /**
     * Seed website analytics.
     */
    private function seedWebsiteAnalytics()
    {
        $this->command->info('🔄 جاري إنشاء بيانات التحليلات...');

        for ($i = 30; $i >= 0; $i--) {
            DB::table('website_analytics')->insert([
                'date' => now()->subDays($i)->format('Y-m-d'),
                'visitors' => rand(100, 500),
                'pageviews' => rand(300, 1500),
                'bounce_rate' => rand(30, 70),
                'avg_session_duration' => rand(60, 300),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ تم إنشاء بيانات التحليلات');
    }
}

