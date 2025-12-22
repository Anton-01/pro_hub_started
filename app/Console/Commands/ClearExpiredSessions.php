<?php

namespace App\Console\Commands;

use App\Models\UserSession;
use Illuminate\Console\Command;

class ClearExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sessions:cleanup';

    /**
     * The console command description.
     */
    protected $description = 'Elimina las sesiones de usuario expiradas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Limpiando sesiones expiradas...');

        $count = UserSession::expired()->delete();

        $this->info("Se eliminaron {$count} sesiones expiradas.");

        return Command::SUCCESS;
    }
}
