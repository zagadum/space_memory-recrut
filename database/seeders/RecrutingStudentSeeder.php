<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class RecrutingStudentSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('recruting_student')) {
            return;
        }

        $email = 'student.seed@example.com';

        $payload = [
            'password' => Hash::make('password123'),
            'enabled' => 1,
            'blocked' => 0,
            'deleted' => 0,
            'surname' => 'Student',
            'lastname' => 'Seed',
            'name' => 'Seed',
            'group_id' => 0,
            'franchisee_id' => 0,
            'teacher_id' => 0,
        ];

        $columns = DB::select('SHOW COLUMNS FROM recruting_student');

        foreach ($columns as $column) {
            $field = $column->Field;
            $type = strtolower((string) $column->Type);
            $nullable = $column->Null === 'YES';
            $default = $column->Default;
            $extra = strtolower((string) $column->Extra);

            if (in_array($field, ['id', 'created_at', 'updated_at'], true)) {
                continue;
            }

            if ($field === 'email') {
                continue;
            }

            if (array_key_exists($field, $payload)) {
                continue;
            }

            if ($nullable || $default !== null || str_contains($extra, 'auto_increment')) {
                continue;
            }

            if (str_contains($type, 'int') || str_contains($type, 'decimal') || str_contains($type, 'float') || str_contains($type, 'double')) {
                $payload[$field] = 0;
                continue;
            }

            if (str_contains($type, 'date') || str_contains($type, 'time') || str_contains($type, 'year')) {
                $payload[$field] = now();
                continue;
            }

            $payload[$field] = '';
        }

        if (Schema::hasColumn('recruting_student', 'updated_at')) {
            $payload['updated_at'] = now();
        }

        if (Schema::hasColumn('recruting_student', 'created_at')) {
            $payload['created_at'] = now();
        }

        $existingColumns = array_flip(Schema::getColumnListing('recruting_student'));
        $payload = array_intersect_key($payload, $existingColumns);

        DB::table('recruting_student')->updateOrInsert(
            ['email' => $email],
            $payload
        );
    }
}

