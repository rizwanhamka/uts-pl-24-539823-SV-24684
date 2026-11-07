<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->check('database', function () {
                DB::connection()->select('select 1');
            }),
            'cache' => $this->check('cache', function () {
                $key = 'health:probe';
                $value = bin2hex(random_bytes(4));

                Cache::put($key, $value, 10);

                if (Cache::get($key) !== $value) {
                    throw new \RuntimeException('Nilai cache tidak sesuai');
                }

                Cache::forget($key);
            }),
        ];

        $failed = array_keys(array_filter($checks, fn ($c) => $c['status'] === 'down'));
        $healthy = $failed === [];

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'failed' => $failed,
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }

    private function check(string $name, callable $probe): array
    {
        $start = microtime(true);

        try {
            $probe();

            return ['status' => 'up', 'latency_ms' => round((microtime(true) - $start) * 1000, 1)];
        } catch (\Throwable $e) {
            Log::error('Pemeriksaan kesehatan gagal', [
                'actor' => 'system',
                'action' => 'health.check',
                'component' => $name,
                'reason' => 'dependency_unreachable',
                'exception' => class_basename($e),
                'error' => $this->sanitize($e->getMessage()),
            ]);

            // Respons ke klien sengaja generik: detail teknis hanya ada di log
            return ['status' => 'down', 'error' => 'Komponen tidak dapat diakses'];
        }
    }

    /** Buang pasangan 'user'@'host' dan batasi panjang pesan error. */
    private function sanitize(string $message): string
    {
        $message = preg_replace("/'[^']*'@'[^']*'/", "'***'@'***'", $message);

        return Str::limit($message, 300);
    }
}
