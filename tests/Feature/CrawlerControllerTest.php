<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\CrawlerService;

class CrawlerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCrawlEndpoint()
    {
        // This test covers the "recursive" scenario (singlePage=false or omitted).
        $this->mock(CrawlerService::class, function ($mock) {
            // We expect the `crawl` method to be called once, 
            // with "http://example.com" and false (for recursion).
            $mock->shouldReceive('crawl')
                 ->once()
                 ->with('http://example.com', false)
                 ->andReturn([
                     [
                         'url' => 'http://example.com',
                         'statusCode' => 200,
                         'wordCount' => 100,
                         'titleLength' => 15,
                         'images' => [],
                         'internalLinks' => [],
                         'externalLinks' => [],
                     ],
                 ]);
        });

        // Make a POST request WITHOUT specifying singlePage 
        // (or we could set "singlePage" => false explicitly).
        $response = $this->postJson('/api/crawl', [
            'url' => 'http://example.com',
            // 'singlePage' => false  // optional
        ]);

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson([
                     [
                         'url' => 'http://example.com',
                         'statusCode' => 200,
                     ],
                 ]);
    }

    public function testCrawlEndpointSinglePage()
    {
        // This test covers the "single page" scenario.
        $this->mock(CrawlerService::class, function ($mock) {
            // We expect the `crawl` method to be called with singlePage=true.
            $mock->shouldReceive('crawl')
                 ->once()
                 ->with('http://example.com', true)
                 ->andReturn([
                     [
                         'url' => 'http://example.com',
                         'statusCode' => 200,
                         'wordCount' => 10,
                         'titleLength' => 12,
                         'images' => [],
                         'internalLinks' => [],
                         'externalLinks' => [],
                     ],
                 ]);
        });

        // Make a POST request specifying singlePage=true
        $response = $this->postJson('/api/crawl', [
            'url' => 'http://example.com',
            'singlePage' => true,
        ]);

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson([
                     [
                         'url' => 'http://example.com',
                         'statusCode' => 200,
                     ],
                 ]);
    }

    public function testCrawlEndpointHandlesException()
    {
        // Mock the CrawlerService to throw an exception
        $this->mock(CrawlerService::class, function ($mock) {
            // If you want to ensure it’s called with singlePage=false, 
            // add ->with('http://example.com', false) or ->withAnyArgs().
            $mock->shouldReceive('crawl')
                 ->once()
                 ->andThrow(new \Exception('Test exception'));
        });

        // Make a POST request to the crawl endpoint
        $response = $this->postJson('/api/crawl', ['url' => 'http://example.com']);

        // Assert the error response
        $response->assertStatus(500)
                 ->assertJson(['error' => 'Test exception']);
    }
}
