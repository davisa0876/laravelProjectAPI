<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\CrawlerService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;

class CrawlerServiceTest extends TestCase
{
    public function testCrawlSinglePage()
    {
        // Create a mock HTTP client with a predefined response
        $mock = new MockHandler([
            new Response(200, [], '<html><title>Test Page</title><body><a href="https://daviamaral.com/page2">Page 2</a></body></html>'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Mock the LoggerInterface
        $logger = $this->createMock(LoggerInterface::class);

        // Create the CrawlerService with mocked dependencies
        $crawlerService = new CrawlerService($client, $logger);

        // Pass `true` to indicate single-page
        $results = $crawlerService->crawl('https://daviamaral.com/', true);

        // Assert the results
        $this->assertCount(1, $results);
        $this->assertEquals('https://daviamaral.com/', $results[0]['url']);
        $this->assertEquals(200, $results[0]['statusCode']);
        $this->assertEquals(2, $results[0]['wordCount']); // "Test Page"
        $this->assertEquals(9, $results[0]['titleLength']);
    }
    
    public function testCrawlHandlesRecursiveDepth()
    {
        $mock = new MockHandler([
            // First page with an internal link to the second page
            new Response(200, [], '<html><title>Example</title><a href="https://daviamaral.com/page2">Page 2</a></html>'),
            // Second page with no links
            new Response(200, [], '<html><title>Page 2</title><p>No links here.</p></html>'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
    
        $logger = $this->createMock(LoggerInterface::class);
    
        $crawlerService = new CrawlerService($client, $logger);
    
        // For recursion, pass `false` or omit the second parameter (defaults to false).
        $results = $crawlerService->crawl('https://daviamaral.com/', false);

        // Assert the results
        $this->assertCount(2, $results); // Two pages crawled
        $this->assertEquals('https://daviamaral.com/', $results[0]['url']);
        $this->assertEquals('https://daviamaral.com/page2', $results[1]['url']);
    }

    public function testCrawlHandlesExceptions()
    {
        // Create a mock HTTP client that throws an exception
        $mock = new MockHandler([
            new \GuzzleHttp\Exception\RequestException('Error', new \GuzzleHttp\Psr7\Request('GET', 'test')),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Mock the LoggerInterface
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
               ->method('error')
               ->with($this->stringContains('Error crawling'));

        // Create the CrawlerService with mocked dependencies
        $crawlerService = new CrawlerService($client, $logger);

        // It's not crucial to pass `true` or `false` here,
        // but we'll just keep it consistent with the default usage (false).
        $results = $crawlerService->crawl('https://daviamaral.com/', false);

        // Assert that no pages were returned due to the exception
        $this->assertEmpty($results);
    }
}
