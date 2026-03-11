<?php
// run this command migration: create table ads
// php artisan make:migration create_ads_table

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Ads extends Model
{
    protected $table = 'ads';

    protected $fillable = [
        'id',
        'img',
        'url',
        'enabled',

    ];
    public $autoProcessMedia = false;
    protected $dates = [
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/ads/'.$this->getKey());
    }





}
