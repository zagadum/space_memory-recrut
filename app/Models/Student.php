<?php

namespace App\Models;

use App\AdminModule\AdminAuth\Activation\Traits\CanActivate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property string $api_token
 */
class Student extends Authenticatable
{
   // use Notifiable;
  //  use CanActivate;
    //protected $connection = 'memory_ua';
    protected $table = 'student';
    /** @var string */
    protected $guard = 'student';
    protected $fillable = [
        'franchisee_id',
        'group_id',
        'teacher_id',
        'email',
        'subcribe_email',
        'password',
        'surname',
        'lastname',
        'patronymic',
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
        'twochildren_id',
        'is_twochildren',
        'rang',
        'rang_level',
        'dob',
        'phone',
        'phone_country',
        'start_day',
        'date_finish',
        'sum_aboniment',
        'discount',
        'balance',
        'diams',
        'language',
        'blocking_reason',
        'email_verified_at',
        'blocked',
        'enabled',
        'deleted',
        'last_login_at',
        'api_token',
        'sess_id',

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

    /* ************************ ACCESSOR ************************* */

    /**
     * Get language in display format (for UI)
     *
     * @return string|null
     */
    public function getDisplayLanguageAttribute(): ?string
    {
        return \App\Services\LocaleService::toDisplay($this->language);
    }

    /**
     * @return HasOne
     */
    public function Franchisee()
    {
        return $this->hasOne(Franchisee::class, 'id', 'franchisee_id');
    }

    /**
     * @return HasOne
     */
    public function Teacher()
    {
        return $this->hasOne(Teacher::class, 'id', 'teacher_id');
    }

    /**
     * @return HasOne
     */
    public function Group()
    {
        return $this->hasOne(TeacherGroup::class, 'id', 'group_id');
    }

    /**
     * @return HasMany
     */
    public function Payments()
    {
        return $this->hasMany(StudentPayment::class, 'student_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function PaymentDocuments()
    {
        return $this->hasMany(PaymentDocument::class, 'student_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function ProgramEvents()
    {
        return $this->hasMany(StudentProgramEvent::class, 'student_id', 'id');
    }
//    public function TwoChildren(){
//        return $this->hasOne(Student::class, 'id ', 'twochildren_id');
//    }

    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getResourceUrlAttribute()
    {
        return url('/admin/students/'.$this->getKey());
    }

    /**
     * Roll API Key
     *
     * @return void
     */
    public function rollApiKey()
    {
        do{
            $this->api_token =Str::random(60);

        }while($this->where('api_token', $this->api_token)->exists());
        $this->save();
    }

    /**
     * Get top students by balance (for ranking)
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopStudents($limit = 3)
    {
        return self::where('enabled', 1)
            ->where('blocked', 0)
            ->where('deleted', 0)
            ->where('balance', '>', 0)
            ->orderBy('balance', 'desc')
            ->limit($limit)
            ->get(['id', 'surname', 'lastname', 'patronymic', 'balance']);
    }

    /**
     * Get avatar URL for student
     *
     * @param int $studentId
     * @return string
     */
    public static function getAvatarUrl($studentId)
    {
        $avatarPath = public_path('useruploads/users/' . $studentId . '_ava.png');

        if (file_exists($avatarPath)) {
            $timestamp = filemtime($avatarPath);
            return asset('useruploads/users/' . $studentId . '_ava.png') . '?v=' . $timestamp;
        }

        return asset('images/default_avatar.png');
    }

    /**
     * Get average training time for student (in minutes)
     *
     * @param int $studentId
     * @return int
     */
    public static function getAverageTrainingTime($studentId)
    {
        // TODO: Calculate from student_training_result table when data is available
        // For now, return default value
        return 12;
    }
}
