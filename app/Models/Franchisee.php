<?php

namespace App\Models;
//use Brackets\AdminAuth\Activation\Traits\CanActivate;
//
//use Brackets\AdminAuth\Notifications\ResetPassword;
//use Brackets\Media\HasMedia\AutoProcessMediaTrait;
//use Brackets\Media\HasMedia\HasMediaCollectionsTrait;
//use Brackets\Media\HasMedia\HasMediaThumbsTrait;
//use Brackets\Media\HasMedia\ProcessMediaTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Helpers\SiteHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class Franchisee extends  Authenticatable  //implements  HasMedia
{
    use Notifiable;
   // use CanActivate;
    //use HasRoles;
  // use AutoProcessMediaTrait;
  //  use HasMediaCollectionsTrait;
  //  use HasMediaThumbsTrait;
  //  use ProcessMediaTrait;
    protected $table = 'franchisee';
    protected $guard = 'franchisee';
    protected $fillable = [
        'email',
        'password',
        'activated',//???
        'last_login_at',
        'surname',
        'first_name',
        'patronymic',
        'country_id',
        'region_id',
        'city_id',
        'locality',
        'phone',
        'phone_country',

        'fin_royalty',
        'fin_pr',
        'fin_legal',
        'fin_address',
        'fin_vid',
        'fin_regno',
        'fin_price_aboniment',
        'fin_currency',
        'passport',
        'iin',
        'subscibe_email',
        'language',
        'enabled',
        'deleted'

    ];

    protected $hidden = [
        'password',
        'remember_token',

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_login_at',
    ];

    protected $appends = ['full_name', 'resource_url'];


    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(app(ResetPassword::class, ['token' => $token]));
    }
    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/franchisees/'.$this->getKey());
    }
    public function City()
    {
        return $this->hasOne(City::class, 'id', 'city_id');

    }
    public function Region()
    {
        return $this->hasOne(Region::class, 'id', 'region_id');

    }
    public function Country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');

    }
    public function Currency()
    {
        return $this->hasOne(Currency::class, 'code', 'fin_currency');

    }
    public function TeacherGroup()
    {
        return $this->hasOne(TeacherGroup::class, 'franchisee_id', 'id');
    }
    public function Teacher()
    {
        return $this->hasOne(Teacher::class, 'franchisee_id', 'id');
    }
    public function Student(){
        return $this->hasOne(Student::class, 'franchisee_id', 'id');
    }
    public function StudentBlock(){
        return $this->hasOne(Student::class, 'franchisee_id', 'id');
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
     * Get url of avatar image
     *
     * @return string|null
     */
    public function getAvatarThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('avatar', 'thumb_150') ?: null;
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
