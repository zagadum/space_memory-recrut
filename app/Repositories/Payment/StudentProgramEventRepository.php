<?php

namespace App\Repositories\Payment;

use App\Models\StudentProgramEvent;

class StudentProgramEventRepository
{
    public function create(array $data): StudentProgramEvent
    {
        if (isset($data['payload']) && is_array($data['payload'])) {
            $data['payload'] = json_encode($data['payload']);
        }

        return StudentProgramEvent::create($data);
    }
}

