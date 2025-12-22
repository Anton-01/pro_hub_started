<?php

namespace App\Events;

use App\Models\CompanyConfiguration;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyConfigurationUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * La configuración actualizada.
     */
    public CompanyConfiguration $configuration;

    /**
     * Create a new event instance.
     */
    public function __construct(CompanyConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }
}
