<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    protected EmailService $emailService;

    /**
     * Create the event listener.
     */
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        if ($user->isAdmin()) {
            $this->emailService->sendWelcomeAdmin($user);
        } else {
            $this->emailService->sendWelcomeUser($user);
        }
    }
}
