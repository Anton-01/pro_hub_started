<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewsCacheInvalidated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * ID de la empresa cuya caché de noticias fue invalidada.
     */
    public string $companyId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $companyId)
    {
        $this->companyId = $companyId;
    }
}
