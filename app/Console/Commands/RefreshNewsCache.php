<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\CacheService;
use Illuminate\Console\Command;

class RefreshNewsCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:refresh-news {--company= : ID de la empresa específica}';

    /**
     * The console command description.
     */
    protected $description = 'Refresca la caché de noticias para todas las empresas o una específica';

    /**
     * Execute the console command.
     */
    public function handle(CacheService $cacheService): int
    {
        $companyId = $this->option('company');

        if ($companyId) {
            $this->info("Refrescando caché de noticias para empresa: {$companyId}");
            $cacheService->invalidateCompanyCache($companyId, 'news');
            $cacheService->getNews($companyId);
            $this->info('Caché de noticias refrescada.');
        } else {
            $companies = Company::active()->get();
            $bar = $this->output->createProgressBar($companies->count());

            $this->info('Refrescando caché de noticias para todas las empresas...');
            $bar->start();

            foreach ($companies as $company) {
                $cacheService->invalidateCompanyCache($company->id, 'news');
                $cacheService->getNews($company->id);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Caché de noticias refrescada para {$companies->count()} empresas.");
        }

        return Command::SUCCESS;
    }
}
