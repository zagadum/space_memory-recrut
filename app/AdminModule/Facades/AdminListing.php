<?php

namespace App\AdminModule\Facades;

use Illuminate\Support\Facades\Facade;


class AdminListing extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'admin-listing';
    }
}
