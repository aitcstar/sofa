<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TaskSchedulerService;
use App\Services\NotificationService;

class RunScheduledTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:run {--limit=10 : Maximum number of tasks to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run pending scheduled tasks';

    private $taskScheduler;
    private $notificationService;

    /**
     * Create a new command instance.
     */
    public function __construct(TaskSchedulerService $taskScheduler, NotificationService $notificationService)
    {
        parent::__construct();
        $this->taskScheduler = $taskScheduler;
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting scheduled tasks execution...');
        
        $limit = $this->option('limit');
        $executed = 0;
        
        try {
            $executed = $this->taskScheduler->runPendingTasks();
            
            $this->info("Executed {$executed} tasks successfully.");
            
            // إرسال التذكيرات الدورية
            $this->info('Sending periodic reminders...');
            $this->notificationService->sendPendingOrderReminders();
            
            // عرض إحصائيات المهام
            $stats = $this->taskScheduler->getTaskStats();
            $this->table(
                ['Status', 'Count'],
                [
                    ['Pending', $stats['pending']],
                    ['Running', $stats['running']],
                    ['Completed Today', $stats['completed_today']],
                    ['Failed Today', $stats['failed_today']],
                    ['Overdue', $stats['overdue']]
                ]
            );
            
        } catch (\Exception $e) {
            $this->error('Error executing tasks: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
