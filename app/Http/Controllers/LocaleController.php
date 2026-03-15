<?php

namespace App\Http\Controllers;

use App\Services\LocaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch the current interface language.
     *
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(string $locale)
    {
        $normalized = LocaleService::normalize($locale);

        if ($normalized) {
            Session::put('locale', $normalized);
        }

        return redirect()->back();
    }
}
