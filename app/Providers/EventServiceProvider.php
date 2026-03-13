<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Исправлено: два listener в одном массиве.
        // Ранее было два отдельных ключа Login::class — PHP молча перезаписывал первый,
        // и EnforceSingleSession никогда не вызывался.
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\EnforceSingleSession::class,
            \App\Listeners\LogSuccessfulLogin::class,
        ],

        // История изменений ученика
        \App\Events\StudentCreatedEvent::class => [
            \App\Listeners\LogStudentHistoryListener::class,
        ],
        \App\Events\StudentUpdatedEvent::class => [
            \App\Listeners\LogStudentHistoryListener::class,
        ],
        \App\Events\StudentArchivedEvent::class => [
            \App\Listeners\LogStudentHistoryListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
