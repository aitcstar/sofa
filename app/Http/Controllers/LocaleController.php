<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function setLocale(Request $request)
    {
        $locale = $request->locale;
        if (in_array($locale, ['ar', 'en'])) {
            session(['locale' => $locale]);
        }
        return response()->json(['status' => 'success']);
    }
}
