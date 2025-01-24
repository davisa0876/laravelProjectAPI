<?php

namespace Tests\Feature\Weather;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;

class WeatherControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_get_weather_data()
    {
        // Create a mock response
        $mockResponse = [
            'main' => [
                'temp' => 20,
                'humidity' => 50
            ],
            'weather' => [
                ['description' => 'clear sky']
            ]
        ];

        // Create mock handler
        $mock = new MockHandler([
            new Response(200, [], json_encode($mockResponse))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->app->instance(Client::class, $client);

        $response = $this->getJson('/api/weather?city=London');

        $response->assertStatus(200)
            ->assertJson($mockResponse);
    }

    public function test_handles_weather_api_error()
    {
        $mock = new MockHandler([
            new Response(500, [], json_encode(['message' => 'API Error']))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->app->instance(Client::class, $client);

        $response = $this->getJson('/api/weather?city=London');

        $response->assertStatus(500)
            ->assertJsonStructure(['error']);
    }
} 