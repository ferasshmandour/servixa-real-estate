<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    /**
     * Persist the chosen UI locale in the session and return to the previous page.
     * The {lang} route parameter is constrained to ['en', 'ar'] at the route level,
     * so no inline validation is needed here.
     */
    public function switch(string $lang): RedirectResponse
    {
        session(['locale' => $lang]);

        return back();
    }
}
