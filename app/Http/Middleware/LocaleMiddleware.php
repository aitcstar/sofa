<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleMiddleware  // ✅ غيرنا الاسم هنا
{
    public function handle($request, Closure $next)
    {
       /* $firstSegment = $request->segment(1);

        if ($firstSegment === 'en') {
            session(['locale' => 'en']);
            App::setLocale('en');
        } else {
            session(['locale' => 'ar']);
            App::setLocale('ar');
        }

        return $next($request);*/




        // ضع اللغة في الجلسة وفي التطبيق
        $locale = session('locale', 'ar');

if ($request->segment(1) === 'en') {
    $locale = 'en';
}

session(['locale' => $locale]);
App::setLocale($locale);

return $next($request);



    }
}
