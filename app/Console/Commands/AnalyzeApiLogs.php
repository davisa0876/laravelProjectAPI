<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AnalyzeApiLogs extends Command
{
    protected $signature = 'api:analyze {--days=1}';
    protected $description = 'Analyze API usage from crawler logs';

    public function handle()
    {
        $days = $this->option('days');
        $logPath = storage_path('logs/api-crawler.log');
        
        if (!File::exists($logPath)) {
            $this->error('No crawler logs found!');
            return 1;
        }

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
                    'count' => $group->count(),
                    'avg_time' => $group->avg('context.execution_time'),
                    'error_rate' => $group->where('context.response_code', '>=', 400)->count() / $group->count() * 100,
                ];
            });

        // Display results
        $this->info("\nAPI Usage Analysis for the last {$days} days:");
        $this->table(
            ['Endpoint', 'Calls', 'Avg Time (ms)', 'Error Rate (%)'],
            $endpointStats->map(function ($stats, $url) {
                return [
                    $url,
                    $stats['count'],
                    round($stats['avg_time'], 2),
                    round($stats['error_rate'], 2),
                ];
            })
        );

        return 0;
    }
} 