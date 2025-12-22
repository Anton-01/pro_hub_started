<?php

namespace App\Console\Commands;

use App\Services\ActivityLogService;
use Illuminate\Console\Command;

class CleanupActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'logs:cleanup {--days=90 : Número de días a mantener}';

    /**
     * The console command description.
     */
    protected $description = 'Elimina logs de actividad antiguos';

    /**
     * Execute the console command.
     */
    public function handle(ActivityLogService $activityLogService): int
    {
        $days = (int) $this->option('days');

        $this->info("Eliminando logs de actividad con más de {$days} días...");

        $count = $activityLogService->cleanup($days);

        $this->info("Se eliminaron {$count} registros de actividad.");

        return Command::SUCCESS;
    }
}
