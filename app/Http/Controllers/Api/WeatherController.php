<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WeatherService;

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
