<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get supported locales from config
        $supportedLocales = config("app.supported_locales", ["en"]);
        $defaultLocale = config("app.locale", "en");

        // Get the Accept-Language header
        $acceptLanguage = $request->header("Accept-Language");

        if ($acceptLanguage) {
            // Parse the Accept-Language header to get preferred languages
            $preferredLocales = $this->parseAcceptLanguage($acceptLanguage);

            // Find the first supported locale from the preferred ones
            foreach ($preferredLocales as $locale) {
                // Check exact match first
                if (in_array($locale, $supportedLocales)) {
                    App::setLocale($locale);
                    return $next($request);
                }

                // Check for language code without region (e.g., 'en' from 'en-US')
                $languageCode = explode("-", $locale)[0];
                if (in_array($languageCode, $supportedLocales)) {
                    App::setLocale($languageCode);
                    return $next($request);
                }
            }
        }

        // If no match found, use the default locale
        App::setLocale($defaultLocale);

        return $next($request);
    }

    /**
     * Parse the Accept-Language header and return an array of locales
     * ordered by their quality values.
     *
     * @param string $acceptLanguage
     * @return array
     */
    private function parseAcceptLanguage(string $acceptLanguage): array
    {
        $locales = [];
        $parts = explode(",", $acceptLanguage);

        foreach ($parts as $part) {
            $part = trim($part);

            if (
                preg_match(
                    '/^([a-z]{2,3}(?:-[a-zA-Z]{2,4})?|\*)(?:;q=([0-9.]+))?$/i',
                    $part,
                    $matches
                )
            ) {
                $locale = strtolower($matches[1]);
                $quality = isset($matches[2]) ? (float) $matches[2] : 1.0;

                // Skip wildcard
                if ($locale !== "*") {
                    $locales[] = [
                        "locale" => $locale,
                        "quality" => $quality,
                    ];
                }
            }
        }

        // Sort by quality in descending order
        usort($locales, function ($a, $b) {
            return $b["quality"] <=> $a["quality"];
        });

        // Return just the locale codes
        return array_column($locales, "locale");
    }
}
