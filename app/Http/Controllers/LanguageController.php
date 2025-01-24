<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switchLang(Request $request)
    {
        $validated = $request->validate([
            'lang' => 'required|string|in:en,es'
        ]);

        App::setLocale($validated['lang']);
        session()->put('locale', $validated['lang']);

        return response()->json([
            'message' => 'Language switched successfully',
            'language' => $validated['lang']
        ]);
    }
} 