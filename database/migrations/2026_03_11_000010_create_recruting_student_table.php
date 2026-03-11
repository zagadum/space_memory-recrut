<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('recruting_student')) {
            return;
        }

        if (!Schema::hasTable('student')) {
            // Fallback для новых окружений без legacy-таблицы student.
            Schema::create('recruting_student', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('franchisee_id')->nullable();
                $table->unsignedBigInteger('group_id')->nullable();
                $table->unsignedBigInteger('teacher_id')->nullable();
                $table->string('email')->unique();
                $table->string('password');
                $table->string('name')->nullable();
                $table->string('surname')->nullable();
                $table->string('lastname')->nullable();
                $table->string('patronymic')->nullable();
                $table->boolean('enabled')->default(1);
                $table->boolean('blocked')->default(0);
                $table->boolean('deleted')->default(0);
                $table->string('api_token', 80)->nullable()->unique();
                $table->string('sess_id')->nullable();
                $table->timestamp('last_login_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });

            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('CREATE TABLE recruting_student LIKE student');
        } elseif ($driver === 'pgsql') {
            DB::statement('CREATE TABLE recruting_student (LIKE student INCLUDING ALL)');
        } elseif ($driver === 'sqlite') {
            DB::statement('CREATE TABLE recruting_student AS SELECT * FROM student WHERE 1 = 0');
        } else {
            throw new RuntimeException('Unsupported database driver: ' . $driver);
        }

        DB::statement('INSERT INTO recruting_student SELECT * FROM student');
    }

    public function down(): void
    {
        Schema::dropIfExists('recruting_student');
    }
};


