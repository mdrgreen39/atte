<?php

namespace App\Listeners;

use App\Events\EmailVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\EmailVerified  $event
     * @return void
     */
    public function handle(EmailVerified $event)
    {
        Mail::to($event->user->email)->send(new WelcomeEmail($event->user));
    }
}
