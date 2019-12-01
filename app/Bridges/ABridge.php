<?php

declare(strict_types=1);

namespace App\Bridges;

use App\Exceptions\Handler;
use Exception;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Log\LogManager;
use ReflectionObject;
use Illuminate\Foundation\Bootstrap\SetRequestForConsole;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Illuminate\Support\Facades\Request as RequestFacade;

abstract class ABridge implements IBridge
{
    private $app;
    private $basePath;

    public function __construct(Application $app, ?string $basePath = null)
    {
        $this->app = $app;
        $this->basePath = $basePath ?? base_path();
        $this->bootstrap();
    }

    public function bootstrap(): void
    {
        $kernel = $this->app->make(Kernel::class);
        $reflection = new ReflectionObject($kernel);
        $bootstrappersMethod = $reflection->getMethod('bootstrappers');
        $bootstrappersMethod->setAccessible(true);
        $bootstrappers = $bootstrappersMethod->invoke($kernel);
        array_splice($bootstrappers, -2, 0, [SetRequestForConsole::class]);
        $this->app->bootstrapWith($bootstrappers);
    }

    protected function getBasePath(): string
    {
        return $this->basePath;
    }

    protected function getLogger(): LogManager
    {
        return $this->app->make('log');
    }

    protected function getErrorHandler(): Handler
    {
        return $this->app->make(Handler::class);
    }

    public function handleRequest(Request $baseRequest): Response
    {
        $request = RequestFacade::createFromBase($baseRequest);

        try {
            $kernel = $this->app->make(Kernel::class);
            $response = $kernel->handle($request);
            $kernel->terminate($request, $response);

            return $response;
        } catch (Exception $e) {
            $this->getErrorHandler()->report($e);

            return $this->getErrorHandler()->render($request, $e);
        } catch (Throwable $e) {
            $this->getLogger()->error(
                'throwable_error',
                [
                    'msg' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'prevMsg' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null,
                    'prevTrace' => $e->getPrevious() ? $e->getPrevious()->getTraceAsString() : null
                ]
            );

            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
