<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = session('locale', 'ar'); // أو أي طريقة تحدد اللغة
        App::setLocale($locale);

        return $next($request);
    }
}
