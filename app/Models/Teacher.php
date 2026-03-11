<?php

namespace App\Models;

use App\AdminModule\AdminAuth\Activation\Traits\CanActivate;
//use Brackets\AdminAuth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



use Illuminate\Contracts\Routing\UrlGenerator;

//use Brackets\Media\HasMedia\HasMediaCollectionsTrait;
//use Brackets\Media\HasMedia\HasMediaThumbsTrait;
//use Brackets\Media\HasMedia\ProcessMediaTrait;
//use Brackets\Media\HasMedia\AutoProcessMediaTrait;


//use Spatie\MediaLibrary\HasMedia;
//use Spatie\MediaLibrary\MediaCollections\Models\Media;
//use Spatie\Permission\Traits\HasRoles;
//use Spatie\Image\Exceptions\InvalidManipulation;

class Teacher extends Authenticatable // implements  HasMedia
{
    use Notifiable;


    use CanActivate;
//
//    use HasRoles;
//    use AutoProcessMediaTrait;
//    use HasMediaCollectionsTrait;
//    use HasMediaThumbsTrait;
//    use ProcessMediaTrait;

    protected $table = 'teacher';
    protected $guard = 'teacher';
    protected $fillable = [

        'surname',
        'first_name',
        'patronymic',

        'email',
        'password',
        'activated',//???
        'last_login_at',

        'franchisee_id',

        'phone',
        'phone_country',
        'dob',

        'passport',
        'iin',
        'subscibe_email',
        'language',
        'fin_cabinet',
        'enabled',
        'deleted',

    ];

    protected $hidden = [
        'password',
        'remember_token',

    ];

    protected $dates = [
        'dob',
        'created_at',
        'updated_at',
        'deleted_at',
        'last_login_at',
    ];

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {

        return url('/admin/teachers/'.$this->getKey());
    }
    /**
     * Full name for admin user
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->surname . ' ' . $this->first_name;
    }
    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(app(ResetPassword::class, ['token' => $token]));
    }

    public function Franchisee(){
        return $this->hasOne(Franchisee::class, 'id', 'franchisee_id');
    }

    public function TeacherGroup()
    {
        return $this->hasOne(TeacherGroup::class, 'teacher_id', 'id');
    }
    public function Student(){
        return $this->hasOne(Student::class, 'teacher_id', 'id');
    }
    public function StudentBlock()
    {
        return $this->hasOne(Student::class, 'teacher_id', 'id');
    }
    /**
     * Get url of avatar image
     *
     * @return string|null
     */
    public function getAvatarThumbUrlAttribute(): ?string
    {
        return null;//$this->getFirstMediaUrl('avatar', 'thumb_150') ?: null;
    }



    /* ************************ MEDIA ************************ */

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->accepts('image/*');
    }

    /**
     * Register media conversions
     *
     * @param Media|null $media
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->autoRegisterThumb200();

        $this->addMediaConversion('thumb_75')
            ->width(75)
            ->height(75)
            ->fit('crop', 75, 75)
            ->optimize()
            ->performOnCollections('avatar')
            ->nonQueued();

        $this->addMediaConversion('thumb_150')
            ->width(150)
            ->height(150)
            ->fit('crop', 150, 150)
            ->optimize()
            ->performOnCollections('avatar')
            ->nonQueued();
    }

    /**
     * Auto register thumb overridden
     */
    public function autoRegisterThumb200()
    {
        $this->getMediaCollections()->filter->isImage()->each(function ($mediaCollection) {
            $this->addMediaConversion('thumb_200')
                ->width(200)
                ->height(200)
                ->fit('crop', 200, 200)
                ->optimize()
                ->performOnCollections($mediaCollection->getName())
                ->nonQueued();
        });
    }

}
