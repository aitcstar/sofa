<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::create([
            'name' => 'مدير النظام',
            'email' => 'admin@sofa.com',
            'password' => bcrypt('password'),
        ]);

        // إنشاء عميل تجريبي
        \App\Models\User::create([
            'name' => 'عميل تجريبي',
            'email' => 'customer@sofa.com',
            'password' => bcrypt('password'),
            'code' => '+966',
            'phone' => '507654321',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
