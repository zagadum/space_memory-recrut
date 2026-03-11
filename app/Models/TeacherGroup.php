<?php

namespace App\Models;

use App\Helpers\SiteHelper;
use Illuminate\Database\Eloquent\Model;

class TeacherGroup extends Model
{
    protected $fillable = [
        'franchisee_id',
        'teacher_id',
        'country_id',
        'region_id',
        'city_id',
        'name',
        'age_id',
        'address',
        'locations',
        'start_day',
        'start_time',
        'workday1',
        'workday2',
        'workday3',
        'workday4',
        'workday5',
        'workday6',
        'workday7',
        'zoom_url',
        'zoom_text',
        'zoom_img',
        'enabled',
        'deleted',



    ];


    protected $dates = [
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/teacher-groups/'.$this->getKey());
    }

    public function Franchisee(){
        return $this->hasOne(Franchisee::class, 'id', 'franchisee_id');
    }
    public function Teacher(){
        return $this->hasOne(Teacher::class, 'id', 'teacher_id');
    }

    public function City()
    {
        return $this->hasOne(City::class, 'id', 'city_id');

    }
    public function Region()
    {
        return $this->hasOne(Region::class, 'id', 'region_id');

    }
    public function Country(){
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
    public function Age(){
        return $this->hasOne(Age::class, 'id', 'age_id');
    }
    public function Student(){
        return $this->hasOne(Student::class, 'group_id', 'id');
    }
    public function StudentBlock()
    {
        return $this->hasOne(Student::class, 'group_id', 'id');
    }

}
