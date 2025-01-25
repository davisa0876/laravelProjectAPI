<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WeatherService;

/**
 * @OA\Get(
 *     path="/weather",
 *     tags={"Weather"},
 *     summary="Get weather information",
 *     description="Retrieves current weather data for a specified city",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="city",
 *         in="query",
 *         description="City name",
 *         required=true,
 *         @OA\Schema(type="string", example="London")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Weather data retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="main", type="object",
 *                 @OA\Property(property="temp", type="number", format="float", example=20.5),
 *                 @OA\Property(property="feels_like", type="number", format="float", example=19.8),
 *                 @OA\Property(property="temp_min", type="number", format="float", example=18.2),
 *                 @OA\Property(property="temp_max", type="number", format="float", example=22.4),
 *                 @OA\Property(property="pressure", type="integer", example=1015),
 *                 @OA\Property(property="humidity", type="integer", example=75)
 *             ),
 *             @OA\Property(property="weather", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=800),
 *                 @OA\Property(property="main", type="string", example="Clear"),
 *                 @OA\Property(property="description", type="string", example="clear sky"),
 *                 @OA\Property(property="icon", type="string", example="01d")
 *             ))
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error"
 *     )
 * )
 */
class WeatherController extends Controller
{
    public function show(Request $request, WeatherService $weatherService)
    {
        $city = $request->query('city', 'London'); // default if needed
        try {
            $data = $weatherService->getWeather($city);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
