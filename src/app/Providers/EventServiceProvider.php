<?php

namespace App\Providers;

use App\Listeners\AssignUserRole;
use App\Events\EmailVerified;
use App\Listeners\SendConfirmationEmail;
use App\Listeners\SendVerifiedEmail;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            //SendEmailVerificationNotification::class,
            SendConfirmationEmail::class,
            AssignUserRole::class,
        ],

        EmailVerified::class => [
            SendWelcomeEmail::class,
        ],

        Verified::class => [
            SendVerifiedEmail::class,
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
