<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function random(int $iterations): int
    {
        $random = 0;
        for ($i = 0; $i < $iterations; $i ++) {
            $random = random_int(0, 999999);
        }

        return $random;
    }

    public function index(): JsonResponse
    {
        /**
         * randomową liczbę
         * nazwa menedżera procesów w którym była odpalona aplikacja
         * pid procesu
         * zmienne z php.ini
         */
        return new JsonResponse(
            [
                'rand' => $this->random(500000),
                'env' => env('APP_ENV', 'production'),
                'type' => env('APP_TYPE', 'unknown'),
                'pid' => getmypid(),
                'php_ini' => [
                    'version' => PHP_VERSION,
                    'date.timezone' => ini_get('date.timezone'),
                    'short_open_tag' => ini_get('short_open_tag'),
                    'log_errors' => ini_get('log_errors'),
                    'error_reporting' => ini_get('error_reporting'),
                    'display_errors' => ini_get('display_errors'),
                    'error_log' => ini_get('error_log'),
                    'memory_limit' => ini_get('memory_limit'),
                    'opcache.enable' => ini_get('opcache.enable'),
                    'opcache.memory_consumption' => ini_get('opcache.memory_consumption'),
                    'opcache.max_accelerated_files' => ini_get('opcache.max_accelerated_files'),
                    'opcache.validate_timestamps' => ini_get('opcache.validate_timestamps'),
                    'realpath_cache_size' => ini_get('realpath_cache_size'),
                    'realpath_cache_ttl' => ini_get('realpath_cache_ttl')
                ],
            ]
        );
    }
}
