<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $initialLocale = App::getLocale();
        Log::info("Initial locale: " . $initialLocale);

        // Check header
        if ($request->hasHeader('Accept-Language')) {
            $locale = $request->header('Accept-Language');
            Log::info("Found Accept-Language header: " . $locale);
            
            if (in_array($locale, ['en', 'es'])) {
                App::setLocale($locale);
                Log::info("Set locale to: " . $locale);
            }
        } 
        // Check query parameter
        else if ($request->has('lang')) {
            $locale = $request->input('lang');
            if (in_array($locale, ['en', 'es'])) {
                App::setLocale($locale);
            }
        } 
        // Check session
        else if (session()->has('locale')) {
            $locale = session()->get('locale');
            if (in_array($locale, ['en', 'es'])) {
                App::setLocale($locale);
            }
        } 
        // Default to English if no valid locale is found
        else {
            App::setLocale('en');
        }

        $finalLocale = App::getLocale();
        Log::info("Final locale: " . $finalLocale);

        return $next($request);
    }
} 