<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Psr\Log\LoggerInterface;

class CrawlerService
{
    private $client;
    private $logger;
    private $maxDepth = 4;

    // Private property to store whether or not we're in single-page mode.
    private $singlePage = false;

    public function __construct(Client $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger; 
    }

    /**
     * Crawl one or more pages starting from $baseUrl.
     *
     * @param  string  $baseUrl
     * @param  bool    $singlePage  If true, crawl only $baseUrl; no recursion.
     * @return array
     */
    public function crawl(string $baseUrl, bool $singlePage = false): array
    {
        // Store the mode in a class property so the recursive function can access it.
        $this->singlePage = $singlePage;

        $visitedUrls = [];
        $pages = [];

        try {
            return $this->crawlPage($baseUrl, 0, $visitedUrls, $pages);
        } catch (\Exception $e) {
            $this->logger->error("CrawlerService: Error crawling {$baseUrl}: " . $e->getMessage());
            throw $e;
        }
    }

    private function crawlPage(string $url, int $depth, array &$visitedUrls, array &$pages): array
    {
        if ($depth > $this->maxDepth || in_array($url, $visitedUrls)) {
            return $pages;
        }
    
        $visitedUrls[] = $url;
    
        try {
            $startTime = microtime(true);
            $response = $this->client->request('GET', $url);
            $loadTime = microtime(true) - $startTime;
    
            $statusCode = $response->getStatusCode();
            $html = (string) $response->getBody();
            $contentLength = strlen($html);
    
            $pageData = $this->extractPageData($html, $url);
    
            $pages[] = [
                'url'             => $url,
                'statusCode'      => $statusCode,
                'loadTimeSeconds' => round($loadTime, 4),
                'contentLength'   => $contentLength,
                'wordCount'       => $pageData['wordCount'],
                'titleLength'     => strlen($pageData['title']),
                'imagesCount'     => count($pageData['images']),
                'internalLinksCount' => count($pageData['internalLinks']),
                'externalLinksCount' => count($pageData['externalLinks']),
                'images'          => $pageData['images'],
                'internalLinks'   => $pageData['internalLinks'],
                'externalLinks'   => $pageData['externalLinks'],
            ];
    
            if (!$this->singlePage) {
                foreach ($pageData['internalLinks'] as $link) {
                    if (!in_array($link, $visitedUrls)) {
                        $this->crawlPage($link, $depth + 1, $visitedUrls, $pages);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error("CrawlerService: Error crawling {$url}: " . $e->getMessage());
        }
    
        return $pages;
    }
    
    

    private function extractPageData(string $html, string $baseUrl): array
    {
        $crawler = new Crawler($html);

        // Handle the possibility of missing <title>
        try {
            $title = $crawler->filter('title')->text();
        } catch (\InvalidArgumentException $e) {
            $title = '';
            $this->logger->warning("No <title> found at {$baseUrl}");
        }

        $wordCount = str_word_count(strip_tags($html));
        $images = $crawler->filter('img')->extract(['src']);

        // Basic internal/external link logic
        $internalLinks = $crawler->filter('a[href^="' . $baseUrl . '"]')->extract(['href']);
        $externalLinks = $crawler->filter('a[href]:not([href^="' . $baseUrl . '"])')->extract(['href']);

        return [
            'title'         => $title,
            'wordCount'     => $wordCount,
            'images'        => array_unique($images),
            'internalLinks' => array_unique($internalLinks),
            'externalLinks' => array_unique($externalLinks),
        ];
    }
}
