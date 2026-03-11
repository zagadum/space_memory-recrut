<?php

namespace Database\Seeders;

use App\Models\RecrutingStudent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class RecrutingStudentSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('recruting_student')) {
            return;
        }

        RecrutingStudent::query()->updateOrCreate(
            ['email' => 'student.seed@example.com'],
            [
                'password' => Hash::make('password123'),
                'name' => 'Seed',
                'surname' => 'Student',
                'enabled' => 1,
                'blocked' => 0,
                'deleted' => 0,
            ]
        );
    }
}

