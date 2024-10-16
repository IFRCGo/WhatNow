<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
{
    
    public function handle($request, Closure $next)
    {
        if ($locale = $this->parseLocale($request)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    
    protected function parseLocale($request)
    {
        $locales = config('app.locales');

                        
        $locale = $request->server('HTTP_ACCEPT_LANGUAGE');
        $locale = substr($locale, 0, strpos($locale, ',') ?: strlen($locale));

        if (isset($locales[$locale])) {
            return $locale;
        }

        $locale = substr($locale, 0, 2);
        if (isset($locales[$locale])) {
            return $locale;
        }
    }
}
