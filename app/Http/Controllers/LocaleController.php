<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function setLocale(Request $request)
    {
        $locale = $request->locale;
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        session(['locale' => $locale]);

        $currentUrl = $request->get('current_url') ?? url()->current();
        $parsed = parse_url($currentUrl);

        $path = $parsed['path'] ?? '/';
        $segments = explode('/', trim($path, '/'));

        if ($locale === 'ar') {
            if (in_array($segments[0] ?? '', ['ar', 'en'])) {
                array_shift($segments);
            }
            $newPath = '/' . implode('/', $segments);
        } else {
            if (!in_array($segments[0] ?? '', ['ar', 'en'])) {
                array_unshift($segments, 'en');
            } else {
                $segments[0] = 'en';
            }
            $newPath = '/' . implode('/', $segments);
        }

        return response()->json([
            'status' => 'success',
            'redirect' => $newPath
        ]);
 }
}
