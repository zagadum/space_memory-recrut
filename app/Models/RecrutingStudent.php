<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property string $api_token
 */
class RecrutingStudent extends Authenticatable
{
   // use Notifiable;
  //  use CanActivate;

    protected $table = 'recruting_student';
    /** @var string */
    protected $guard = 'student';
    protected $fillable = [
        // === Поля из student.sql (ЭТАЛОН — НЕ ТРОГАТЬ) ===
        'franchisee_id', 'practicant_id', 'group_id', 'teacher_id',
        'email', 'subcribe_email', 'password',
        'surname', 'lastname', 'patronymic',
        'dob', 'phone', 'phone_country',

        // Parent 1 (используется при регистрации)
        'parent1_surname', 'parent1_lastname', 'parent1_patronymic',
        'parent1_phone', 'parent1_phone_country',

        // Parent 2, 3 (legacy, не используем при регистрации, но поля существуют)
        'parent2_surname', 'parent2_first_name', 'parent2_patronymic',
        'parent2_phone', 'parent2_phone_country',
        'parent3_surname', 'parent3_first_name', 'parent3_patronymic',
        'parent3_phone', 'parent3_phone_country',

        // Учебный процесс
        'start_day', 'date_finish', 'sum_aboniment',
        'is_twochildren', 'twochildren_id',
        'discount', 'balance', 'diams', 'rang', 'rang_level',
        'language', 'blocking_date', 'blocking_reason',
        'blocked', 'enabled', 'deleted',
        'project_id',

        // Системные
        'email_verified_at', 'last_login_at',
        'remember_token', 'api_token', 'sess_id',

        // === Расширения recruting_student (МОЖНО менять) ===
        'status', 'subject', 'verification_code',
        'name', // Используется в форме, но не в эталоне
        'parent_name', 'parent_surname', 'parent_phone', 'parent_passport',
        'country', 'country_id', 'city', 'address', 'zip', 'apartment',
        'photo_consent', 'reg_comment',
        'terms_accepted', 'privacy_accepted',
        'data_processing_accepted', 'urgent_start_accepted',
        'recording_consent_accepted', 'marketing_consent_accepted',
        'email_sent', 'email_sent_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'sess_id'

    ];

    protected $dates = [
        'start_day',
        'date_finish',
        'email_verified_at',
        'created_at',
        'updated_at',
        'last_login_at'
    ];

    protected $appends = ['resource_url'];

}

