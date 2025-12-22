<?php

namespace App\Events;

use App\Models\User;
use App\Models\PasswordResetToken;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordResetRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * El usuario que solicitó el restablecimiento.
     */
    public User $user;

    /**
     * El token de restablecimiento.
     */
    public PasswordResetToken $token;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, PasswordResetToken $token)
    {
        $this->user = $user;
        $this->token = $token;
    }
}
