<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

final class LanguageController extends Controller
{
    /**
     * Switch the application locale
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        App::setLocale($locale);
        session()->put('locale', $locale);

        return back();
    }
}
