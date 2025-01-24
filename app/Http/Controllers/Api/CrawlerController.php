<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CrawlerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;

class CrawlerController extends Controller
{
    private $crawlerService;
    private $logger;

    public function __construct(CrawlerService $crawlerService, LoggerInterface $logger)
    {
        $this->crawlerService = $crawlerService;
        $this->logger = $logger;
    }

    public function crawl(Request $request)
    {
        try {
            $baseUrl = $request->input('url');
            // 1) Single-page or full recursion?
            $singlePage = (bool) $request->input('singlePage', false);

            // 2) Pass singlePage to the service
            $results = $this->crawlerService->crawl($baseUrl, $singlePage);

            return response()->json($results, Response::HTTP_OK); 
        } catch (\Exception $e) {
            $this->logger->error("CrawlerController: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
