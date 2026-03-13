<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlsProjectsSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'id'         => 1,
                'code'       => 'space_memory',
                'name'       => 'Space Memory',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 2,
                'code'       => 'indigo',
                'name'       => 'Indigo',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // upsert — безопасно при повторном запуске seeder
        DB::table('gls_projects')->upsert(
            $projects,
            uniqueBy: ['id'],
            update:   ['code', 'name', 'is_active', 'updated_at']
        );
    }
}
