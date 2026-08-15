<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('Accept-Language');

        if ($locale && in_array($locale, array_keys(config('app.available_locales', ['ar' => 'Arabic', 'en' => 'English'])))) {
            App::setLocale($locale);
        } elseif ($request->user()) {
            App::setLocale($request->user()->language_preference ?? config('app.locale', 'ar'));
        } else {
            App::setLocale(config('app.locale', 'ar'));
        }

        return $next($request);
    }
}
