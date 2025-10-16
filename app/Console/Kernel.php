<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // تشغيل المهام المجدولة كل دقيقة
        $schedule->command('scheduled-tasks:run')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();

        // تنظيف السجلات القديمة يومياً
        $schedule->command('logs:cleanup')
                 ->daily()
                 ->at('02:00');

        // إرسال تقارير يومية
        $schedule->command('reports:daily')
                 ->dailyAt('08:00');

        // تحديث الإحصائيات كل ساعة
        $schedule->command('analytics:update')
                 ->hourly();

        // فحص الطلبات المتأخرة كل ساعتين
        $schedule->command('orders:check-delayed')
                 ->cron('0 */2 * * *');

        // إرسال تذكيرات المتابعة
        $schedule->command('leads:send-reminders')
                 ->hourly();

        // تنظيف الملفات المؤقتة أسبوعياً
        $schedule->command('files:cleanup')
                 ->weekly()
                 ->sundays()
                 ->at('03:00');

        // نسخ احتياطي من قاعدة البيانات يومياً
        $schedule->command('backup:run')
                 ->daily()
                 ->at('01:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
