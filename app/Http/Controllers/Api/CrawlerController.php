<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CrawlerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Crawler",
 *     description="API Endpoints for analyzing API usage and logs"
 * )
 */
class CrawlerController extends Controller
{
    private $crawlerService;
    private $logger;

    public function __construct(CrawlerService $crawlerService, LoggerInterface $logger)
    {
        $this->crawlerService = $crawlerService;
        $this->logger = $logger;
    }

    /**
     * @OA\Get(
     *     path="/crawler/analyze",
     *     tags={"Crawler"},
     *     summary="Analyze API usage statistics",
     *     description="Retrieves statistics about API usage including endpoint calls, response times, and error rates",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Number of days to analyze",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Analysis results retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="endpoints", type="array", @OA\Items(
     *                 @OA\Property(property="url", type="string", example="/api/weather"),
     *                 @OA\Property(property="calls", type="integer", example=150),
     *                 @OA\Property(property="avg_time", type="number", format="float", example=45.23),
     *                 @OA\Property(property="error_rate", type="number", format="float", example=2.5)
     *             )),
     *             @OA\Property(property="total_calls", type="integer", example=450),
     *             @OA\Property(property="avg_response_time", type="number", format="float", example=38.67),
     *             @OA\Property(property="overall_error_rate", type="number", format="float", example=1.8)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - User doesn't have required permissions"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function analyze(Request $request)
    {
        $days = $request->query('days', 1);
        $logPath = storage_path('logs/api-crawler.log');
        
        if (!File::exists($logPath)) {
            return response()->json([
                'error' => 'No crawler logs found'
            ], 404);
        }

        try {
            $logs = collect(file($logPath))
                ->filter(function ($line) use ($days) {
                    return Str::contains($line, now()->subDays($days)->format('Y-m-d'));
                })
                ->map(function ($line) {
                    return json_decode($line, true);
                })
                ->filter();

            // Analyze endpoints usage
            $endpointStats = $logs->groupBy('context.url')
                ->map(function ($group) {
                    return [
                        'url' => $group->first()['context']['url'],
                        'calls' => $group->count(),
                        'avg_time' => round($group->avg('context.execution_time'), 2),
                        'error_rate' => round($group->where('context.response_code', '>=', 400)->count() / $group->count() * 100, 2),
                    ];
                })->values();

            // Calculate overall statistics
            $totalCalls = $logs->count();
            $avgResponseTime = round($logs->avg('context.execution_time'), 2);
            $overallErrorRate = round($logs->where('context.response_code', '>=', 400)->count() / $totalCalls * 100, 2);

            return response()->json([
                'endpoints' => $endpointStats,
                'total_calls' => $totalCalls,
                'avg_response_time' => $avgResponseTime,
                'overall_error_rate' => $overallErrorRate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error analyzing logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/crawler/logs",
     *     tags={"Crawler"},
     *     summary="Get raw API logs",
     *     description="Retrieves raw API logs for detailed analysis",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Number of days of logs to retrieve",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Filter logs by URL pattern",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logs retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="timestamp", type="string", format="date-time"),
     *                 @OA\Property(property="method", type="string", example="GET"),
     *                 @OA\Property(property="url", type="string", example="/api/weather"),
     *                 @OA\Property(property="status", type="integer", example=200),
     *                 @OA\Property(property="duration", type="string", example="45.23ms"),
     *                 @OA\Property(property="ip", type="string", example="127.0.0.1"),
     *                 @OA\Property(property="user_id", type="string", example="1")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function logs(Request $request)
    {
        $days = $request->query('days', 1);
        $filter = $request->query('filter');
        $logPath = storage_path('logs/api-crawler.log');

        if (!File::exists($logPath)) {
            return response()->json([
                'error' => 'No logs found'
            ], 404);
        }

        try {
            $logs = collect(file($logPath))
                ->filter(function ($line) use ($days) {
                    return Str::contains($line, now()->subDays($days)->format('Y-m-d'));
                })
                ->map(function ($line) {
                    $data = json_decode($line, true);
                    return [
                        'timestamp' => $data['context']['timestamp'] ?? null,
                        'method' => $data['context']['method'] ?? null,
                        'url' => $data['context']['url'] ?? null,
                        'status' => $data['context']['response_code'] ?? null,
                        'duration' => $data['context']['execution_time'] ?? null,
                        'ip' => $data['context']['ip'] ?? null,
                        'user_id' => $data['context']['user_id'] ?? null,
                    ];
                });

            if ($filter) {
                $logs = $logs->filter(function ($log) use ($filter) {
                    return Str::contains($log['url'], $filter);
                });
            }

            return response()->json($logs->values());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error retrieving logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/crawler/crawl",
     *     tags={"Crawler"},
     *     summary="Crawl a website",
     *     description="Crawls a specified URL and returns the crawling results. Can perform either single-page or recursive crawling.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url"},
     *             @OA\Property(
     *                 property="url",
     *                 type="string",
     *                 format="uri",
     *                 example="https://example.com",
     *                 description="The base URL to crawl"
     *             ),
     *             @OA\Property(
     *                 property="singlePage",
     *                 type="boolean",
     *                 example=false,
     *                 description="If true, only crawls the specified URL. If false, performs recursive crawling."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Crawling completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="urls",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="url",
     *                         type="string",
     *                         example="https://example.com/page1"
     *                     ),
     *                     @OA\Property(
     *                         property="title",
     *                         type="string",
     *                         example="Page Title"
     *                     ),
     *                     @OA\Property(
     *                         property="status",
     *                         type="integer",
     *                         example=200
     *                     ),
     *                     @OA\Property(
     *                         property="crawled_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2024-03-20T15:30:00Z"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="stats",
     *                 type="object",
     *                 @OA\Property(property="total_urls", type="integer", example=25),
     *                 @OA\Property(property="successful_crawls", type="integer", example=23),
     *                 @OA\Property(property="failed_crawls", type="integer", example=2),
     *                 @OA\Property(property="crawl_time", type="number", format="float", example=5.23)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid URL format"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error during crawling: Connection timeout"
     *             )
     *         )
     *     )
     * )
     */
    public function crawl(Request $request)
    {
        try {
            $baseUrl = $request->input('url');
            $singlePage = (bool) $request->input('singlePage', false);

            $results = $this->crawlerService->crawl($baseUrl, $singlePage);

            return response()->json($results, Response::HTTP_OK); 
        } catch (\Exception $e) {
            $this->logger->error("CrawlerController: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
