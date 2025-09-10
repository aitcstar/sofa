<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
{
    $supportedLocales = ['ar', 'en'];
    $firstSegment = $request->segment(1);

    if (in_array($firstSegment, $supportedLocales)) {
        session(['locale' => $firstSegment]);
        App::setLocale($firstSegment);
    } else {
        $locale = session('locale', 'ar');
        App::setLocale($locale);
    }

    return $next($request);
}


}
