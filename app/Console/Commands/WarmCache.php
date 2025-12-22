<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\CacheService;
use Illuminate\Console\Command;

class WarmCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:warm {--company= : ID de la empresa específica}';

    /**
     * The console command description.
     */
    protected $description = 'Calienta la caché para todas las empresas o una específica';

    /**
     * Execute the console command.
     */
    public function handle(CacheService $cacheService): int
    {
        $companyId = $this->option('company');

        if ($companyId) {
            $this->info("Calentando caché para empresa: {$companyId}");
            $results = $cacheService->warmCache($companyId);
            $this->displayResults($results);
        } else {
            $companies = Company::active()->get();
            $bar = $this->output->createProgressBar($companies->count());

            $this->info('Calentando caché para todas las empresas...');
            $bar->start();

            foreach ($companies as $company) {
                $cacheService->warmCache($company->id);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Caché calentada para {$companies->count()} empresas.");
        }

        return Command::SUCCESS;
    }

    /**
     * Mostrar resultados del calentamiento de caché.
     */
    protected function displayResults(array $results): void
    {
        $this->table(
            ['Tipo', 'Estado'],
            collect($results)->map(function ($status, $type) {
                return [$type, $status ? '✓ OK' : '✗ Error'];
            })->toArray()
        );
    }
}
