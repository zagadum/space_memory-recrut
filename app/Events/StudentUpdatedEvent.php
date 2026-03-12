<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentUpdatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $studentId,
        public string $detail,
        public ?string $changedBy = null,
        public ?array $meta = null
    ) {}
}
