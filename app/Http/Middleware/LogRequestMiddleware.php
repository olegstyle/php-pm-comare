<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestMiddleware
{
    public function handle(Request $request, callable $next)
    {
        if (!config('logging.http.request')) {
            return $next($request);
        }

        Log::info('request_incoming', [
            'ip' => $request->ip(),
            'uri' => $request->path(),
            'body' => (string) $request->getContent(),
            'headers' => $this->filterHeaders($request->header()),
            'method' => $request->getMethod(),
        ]);

        return $next($request);
    }

    public function terminate($request, Response $response): void
    {
        if (!config('logging.http.response')) {
            return;
        }

        Log::info('request_response', [
            'code' => $response->getStatusCode(),
            'body' => config('logging.http.response_content') ? (string) $response->getContent() : null,
            'headers' => $this->filterHeaders($response->headers->all())
        ]);
    }

    private function filterHeaders(array $headers): array
    {
        if (!config('logging.http.headers')) {
            return [];
        }

        return collect($headers)->transform(static function ($item) {
            return $item[0] ?? null;
        })->toArray();
    }
}

