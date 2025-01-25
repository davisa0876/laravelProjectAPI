<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * @OA\Post(
 *     path="/language",
 *     tags={"Language"},
 *     summary="Switch application language",
 *     description="Changes the application language between supported locales",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"lang"},
 *             @OA\Property(property="lang", type="string", enum={"en", "es"}, example="es")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Language switched successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Language switched successfully"),
 *             @OA\Property(property="language", type="string", example="es")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */
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