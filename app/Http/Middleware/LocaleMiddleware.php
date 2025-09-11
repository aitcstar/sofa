<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class LocaleMiddleware  // ✅ غيرنا الاسم هنا
{
    public function handle($request, Closure $next)
    {
        $firstSegment = $request->segment(1);

        if ($firstSegment === 'en') {
            session(['locale' => 'en']);
            App::setLocale('en');
        } else {
            session(['locale' => 'ar']);
            App::setLocale('ar');
        }

        return $next($request);
    }
}
