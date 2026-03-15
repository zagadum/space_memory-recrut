<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Восстановление обязательных полей из эталона student (БД memory)
     * Таблица recruting_student — ЗЕРКАЛО таблицы student
     */
    public function up(): void
    {
        if (!Schema::hasTable('recruting_student')) {
            return;
        }

        Schema::table('recruting_student', function (Blueprint $table) {
            // Базовые поля студента (эталон student.sql)
            if (!Schema::hasColumn('recruting_student', 'practicant_id')) {
                $table->unsignedBigInteger('practicant_id')->nullable()->after('franchisee_id');
            }
            if (!Schema::hasColumn('recruting_student', 'phone')) {
                $table->string('phone', 50)->nullable()->after('patronymic');
            }
            if (!Schema::hasColumn('recruting_student', 'phone_country')) {
                $table->string('phone_country', 10)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('recruting_student', 'subcribe_email')) {
                $table->boolean('subcribe_email')->default(0)->after('email');
            }

            // Parent 1 (основной родитель — используется в регистрации)
            if (!Schema::hasColumn('recruting_student', 'parent1_surname')) {
                $table->string('parent1_surname', 255)->nullable()->after('phone_country');
            }
            if (!Schema::hasColumn('recruting_student', 'parent1_lastname')) {
                $table->string('parent1_lastname', 255)->nullable()->after('parent1_surname');
            }
            if (!Schema::hasColumn('recruting_student', 'parent1_patronymic')) {
                $table->string('parent1_patronymic', 255)->nullable()->after('parent1_lastname');
            }
            if (!Schema::hasColumn('recruting_student', 'parent1_phone')) {
                $table->string('parent1_phone', 50)->nullable()->after('parent1_patronymic');
            }
            if (!Schema::hasColumn('recruting_student', 'parent1_phone_country')) {
                $table->string('parent1_phone_country', 10)->nullable()->after('parent1_phone');
            }

            // Parent 2 (legacy, не используется при регистрации)
            if (!Schema::hasColumn('recruting_student', 'parent2_surname')) {
                $table->string('parent2_surname', 255)->nullable()->after('parent1_phone_country');
            }
            if (!Schema::hasColumn('recruting_student', 'parent2_first_name')) {
                $table->string('parent2_first_name', 255)->nullable()->after('parent2_surname');
            }
            if (!Schema::hasColumn('recruting_student', 'parent2_patronymic')) {
                $table->string('parent2_patronymic', 255)->nullable()->after('parent2_first_name');
            }
            if (!Schema::hasColumn('recruting_student', 'parent2_phone')) {
                $table->string('parent2_phone', 50)->nullable()->after('parent2_patronymic');
            }
            if (!Schema::hasColumn('recruting_student', 'parent2_phone_country')) {
                $table->string('parent2_phone_country', 10)->nullable()->after('parent2_phone');
            }

            // Parent 3 (legacy, не используется при регистрации)
            if (!Schema::hasColumn('recruting_student', 'parent3_surname')) {
                $table->string('parent3_surname', 255)->nullable()->after('parent2_phone_country');
            }
            if (!Schema::hasColumn('recruting_student', 'parent3_first_name')) {
                $table->string('parent3_first_name', 255)->nullable()->after('parent3_surname');
            }
            if (!Schema::hasColumn('recruting_student', 'parent3_patronymic')) {
                $table->string('parent3_patronymic', 255)->nullable()->after('parent3_first_name');
            }
            if (!Schema::hasColumn('recruting_student', 'parent3_phone')) {
                $table->string('parent3_phone', 50)->nullable()->after('parent3_patronymic');
            }
            if (!Schema::hasColumn('recruting_student', 'parent3_phone_country')) {
                $table->string('parent3_phone_country', 10)->nullable()->after('parent3_phone');
            }

            // Учебный процесс (эталон student.sql)
            if (!Schema::hasColumn('recruting_student', 'start_day')) {
                $table->date('start_day')->nullable()->after('parent3_phone_country');
            }
            if (!Schema::hasColumn('recruting_student', 'date_finish')) {
                $table->date('date_finish')->nullable()->after('start_day');
            }
            if (!Schema::hasColumn('recruting_student', 'sum_aboniment')) {
                $table->decimal('sum_aboniment', 10, 2)->nullable()->after('date_finish');
            }
            if (!Schema::hasColumn('recruting_student', 'is_twochildren')) {
                $table->boolean('is_twochildren')->default(0)->after('sum_aboniment');
            }
            if (!Schema::hasColumn('recruting_student', 'twochildren_id')) {
                $table->unsignedBigInteger('twochildren_id')->nullable()->after('is_twochildren');
            }
            if (!Schema::hasColumn('recruting_student', 'discount')) {
                $table->decimal('discount', 5, 2)->nullable()->after('twochildren_id');
            }
            if (!Schema::hasColumn('recruting_student', 'balance')) {
                $table->decimal('balance', 10, 2)->default(0)->after('discount');
            }
            if (!Schema::hasColumn('recruting_student', 'diams')) {
                $table->integer('diams')->default(0)->after('balance');
            }
            if (!Schema::hasColumn('recruting_student', 'rang')) {
                $table->integer('rang')->default(0)->after('diams');
            }
            if (!Schema::hasColumn('recruting_student', 'rang_level')) {
                $table->integer('rang_level')->default(0)->after('rang');
            }

            // Блокировка (эталон student.sql)
            if (!Schema::hasColumn('recruting_student', 'blocking_date')) {
                $table->date('blocking_date')->nullable()->after('rang_level');
            }
            if (!Schema::hasColumn('recruting_student', 'blocking_reason')) {
                $table->text('blocking_reason')->nullable()->after('blocking_date');
            }

            // Проект (эталон student.sql)
            if (!Schema::hasColumn('recruting_student', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('blocking_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('recruting_student')) {
            return;
        }

        Schema::table('recruting_student', function (Blueprint $table) {
            $colsToDrop = [
                'practicant_id',
                'phone',
                'phone_country',
                'subcribe_email',
                'parent1_surname',
                'parent1_lastname',
                'parent1_patronymic',
                'parent1_phone',
                'parent1_phone_country',
                'parent2_surname',
                'parent2_first_name',
                'parent2_patronymic',
                'parent2_phone',
                'parent2_phone_country',
                'parent3_surname',
                'parent3_first_name',
                'parent3_patronymic',
                'parent3_phone',
                'parent3_phone_country',
                'start_day',
                'date_finish',
                'sum_aboniment',
                'is_twochildren',
                'twochildren_id',
                'discount',
                'balance',
                'diams',
                'rang',
                'rang_level',
                'blocking_date',
                'blocking_reason',
                'project_id',
            ];

            foreach ($colsToDrop as $col) {
                if (Schema::hasColumn('recruting_student', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
