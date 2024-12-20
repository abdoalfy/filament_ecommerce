<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('lang', 'en');
        $lang = ($lang === 'ar') ? 'ar' : 'en'; 
        app()->setlocale($lang);    
        return $next($request);
    }
}
