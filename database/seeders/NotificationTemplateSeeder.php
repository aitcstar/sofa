<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // قوالب البريد الإلكتروني
            [
                'name' => 'طلب جديد - بريد إلكتروني',
                'type' => 'order_created',
                'channel' => 'email',
                'subject' => 'تأكيد استلام الطلب رقم {{order_number}}',
                'content' => '
                    <div dir="rtl" style="font-family: Arial, sans-serif;">
                        <h2>مرحباً {{customer_name}}</h2>
                        <p>تم استلام طلبكم رقم <strong>{{order_number}}</strong> بنجاح.</p>
                        <p><strong>تفاصيل الطلب:</strong></p>
                        <ul>
                            <li>رقم الطلب: {{order_number}}</li>
                            <li>الباكج: {{package_name}}</li>
                            <li>عدد الوحدات: {{units_count}}</li>
                            <li>تاريخ الطلب: {{created_at}}</li>
                        </ul>
                        <p>سيتم التواصل معكم قريباً لتأكيد التفاصيل.</p>
                        <p>شكراً لثقتكم في خدماتنا.</p>
                    </div>
                ',
                'variables' => [
                    'customer_name', 'order_number', 'package_name', 'units_count', 'created_at'
                ]
            ],
            [
                'name' => 'تغيير حالة الطلب - بريد إلكتروني',
                'type' => 'order_status_changed',
                'channel' => 'email',
                'subject' => 'تحديث حالة الطلب رقم {{order_number}}',
                'content' => '
                    <div dir="rtl" style="font-family: Arial, sans-serif;">
                        <h2>مرحباً {{customer_name}}</h2>
                        <p>تم تحديث حالة طلبكم رقم <strong>{{order_number}}</strong></p>
                        <p><strong>الحالة الجديدة:</strong> {{new_status}}</p>
                        <p>يمكنكم متابعة تطورات طلبكم من خلال الرابط التالي:</p>
                        <a href="{{order_url}}" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">عرض الطلب</a>
                        <p>شكراً لثقتكم في خدماتنا.</p>
                    </div>
                ',
                'variables' => [
                    'customer_name', 'order_number', 'new_status', 'order_url'
                ]
            ],
            [
                'name' => 'استلام دفعة - بريد إلكتروني',
                'type' => 'payment_received',
                'channel' => 'email',
                'subject' => 'تأكيد استلام الدفعة للطلب رقم {{order_number}}',
                'content' => '
                    <div dir="rtl" style="font-family: Arial, sans-serif;">
                        <h2>مرحباً {{customer_name}}</h2>
                        <p>تم استلام دفعتكم بنجاح.</p>
                        <p><strong>تفاصيل الدفعة:</strong></p>
                        <ul>
                            <li>رقم الطلب: {{order_number}}</li>
                            <li>المبلغ: {{payment_amount}} ريال</li>
                            <li>طريقة الدفع: {{payment_method}}</li>
                            <li>تاريخ الدفع: {{payment_date}}</li>
                        </ul>
                        <p>شكراً لكم.</p>
                    </div>
                ',
                'variables' => [
                    'customer_name', 'order_number', 'payment_amount', 'payment_method', 'payment_date'
                ]
            ],

            // قوالب الإشعارات الداخلية
            [
                'name' => 'طلب جديد - إشعار داخلي',
                'type' => 'order_created',
                'channel' => 'database',
                'subject' => 'طلب جديد رقم {{order_number}}',
                'content' => 'تم إنشاء طلب جديد رقم {{order_number}} من العميل {{customer_name}}',
                'variables' => [
                    'order_number', 'customer_name', 'package_name'
                ]
            ],
            [
                'name' => 'طلب متأخر - إشعار داخلي',
                'type' => 'order_overdue',
                'channel' => 'database',
                'subject' => 'طلب متأخر رقم {{order_number}}',
                'content' => 'الطلب رقم {{order_number}} متأخر عن موعد التسليم بـ {{days_overdue}} يوم',
                'variables' => [
                    'order_number', 'customer_name', 'days_overdue', 'expected_date'
                ]
            ],
            [
                'name' => 'تعيين موظف - إشعار داخلي',
                'type' => 'employee_assigned',
                'channel' => 'database',
                'subject' => 'تم تعيينك للطلب رقم {{order_number}}',
                'content' => 'تم تعيينك للطلب رقم {{order_number}} من العميل {{customer_name}}',
                'variables' => [
                    'order_number', 'customer_name', 'package_name'
                ]
            ],
            [
                'name' => 'استلام دفعة - إشعار داخلي',
                'type' => 'payment_received',
                'channel' => 'database',
                'subject' => 'دفعة جديدة للطلب رقم {{order_number}}',
                'content' => 'تم استلام دفعة بقيمة {{payment_amount}} ريال للطلب رقم {{order_number}}',
                'variables' => [
                    'order_number', 'payment_amount', 'payment_method', 'customer_name'
                ]
            ],

            // قوالب الرسائل النصية
            [
                'name' => 'طلب جديد - رسالة نصية',
                'type' => 'order_created',
                'channel' => 'sms',
                'subject' => 'تأكيد الطلب',
                'content' => 'تم استلام طلبكم رقم {{order_number}} بنجاح. سيتم التواصل معكم قريباً. شكراً لثقتكم.',
                'variables' => [
                    'order_number', 'customer_name'
                ]
            ],
            [
                'name' => 'تغيير حالة الطلب - رسالة نصية',
                'type' => 'order_status_changed',
                'channel' => 'sms',
                'subject' => 'تحديث الطلب',
                'content' => 'تم تحديث حالة طلبكم رقم {{order_number}} إلى: {{new_status}}',
                'variables' => [
                    'order_number', 'new_status'
                ]
            ],

            // قوالب واتساب
            [
                'name' => 'طلب جديد - واتساب',
                'type' => 'order_created',
                'channel' => 'whatsapp',
                'subject' => 'تأكيد الطلب',
                'content' => '
                    مرحباً {{customer_name}} 👋
                    
                    تم استلام طلبكم رقم *{{order_number}}* بنجاح ✅
                    
                    📦 الباكج: {{package_name}}
                    🏠 عدد الوحدات: {{units_count}}
                    📅 تاريخ الطلب: {{created_at}}
                    
                    سيتم التواصل معكم قريباً لتأكيد التفاصيل.
                    
                    شكراً لثقتكم في خدماتنا 🙏
                ',
                'variables' => [
                    'customer_name', 'order_number', 'package_name', 'units_count', 'created_at'
                ]
            ]
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                [
                    'type' => $template['type'],
                    'channel' => $template['channel']
                ],
                $template
            );
        }
    }
}
