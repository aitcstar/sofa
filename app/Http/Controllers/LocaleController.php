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

        // الرابط الحالي
        $currentUrl = $request->get('current_url') ?? url()->current();
        $parsed = parse_url($currentUrl);

        $path = $parsed['path'] ?? '/';
        $segments = explode('/', trim($path, '/'));

        // إزالة أي لغة موجودة في بداية الرابط
        if (in_array($segments[0] ?? '', ['ar', 'en'])) {
            array_shift($segments);
        }

        // إضافة اللغة الجديدة
        array_unshift($segments, $locale);

        // استخدام رابط نسبي فقط
        $newPath = '/' . implode('/', $segments);

        return response()->json([
            'status' => 'success',
            'redirect' => $newPath // بدلًا من الرابط الكامل
        ]);
    }


}
