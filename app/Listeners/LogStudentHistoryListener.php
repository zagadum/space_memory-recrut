<?php

namespace App\Listeners;

use App\Events\StudentCreatedEvent;
use App\Events\StudentUpdatedEvent;
use App\Events\StudentArchivedEvent;
use App\Models\RecrutingStudentHistory;

class LogStudentHistoryListener
{
    public function handle(object $event): void
    {
        $eventName = match(true) {
            $event instanceof StudentCreatedEvent  => 'Ученик создан',
            $event instanceof StudentUpdatedEvent  => 'Данные изменены',
            $event instanceof StudentArchivedEvent => 'Архивирован',
            default => 'Событие'
        };

        RecrutingStudentHistory::create([
            'student_id' => $event->studentId,
            'event'      => $eventName,
            'detail'     => $event->detail,
            'changed_by' => $event->changedBy,
            'meta'       => $event->meta,
        ]);
    }
}
