<?php

declare(strict_types=1);

namespace App\Bridges;

use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface IBridge
{
    public function __construct(Application $app, ?string $basePath = null);

    public function bootstrap(): void;

    public function start(): void;

    public function handleRequest(Request $request): Response;
}
