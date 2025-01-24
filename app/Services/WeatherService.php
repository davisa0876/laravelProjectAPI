<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->baseUrl = config('services.weather.url'); // or env('WEATHER_API_URL')
        $this->apiKey = config('services.weather.key');  // or env('WEATHER_API_KEY')
    }

    public function getWeather($city)
    {
        try {
            // e.g., GET https://api.openweathermap.org/data/2.5/weather?q={city}&appid={API key}
            $response = $this->client->request('GET', $this->baseUrl.'/weather', [
                'query' => [
                    'q'     => $city,
                    'units' => 'metric',
                    'appid' => $this->apiKey,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error("WeatherService: Error fetching weather for {$city}: ".$e->getMessage());
            throw $e;
        }
    }
}
