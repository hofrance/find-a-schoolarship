<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si une langue est spécifiée en paramètre
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            if (in_array($locale, array_keys(config('app.available_locales')))) {
                Session::put('locale', $locale);
                App::setLocale($locale);
            }
        } 
        // Sinon, utiliser la langue de session
        elseif (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        // Sinon, utiliser la langue par défaut
        else {
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
