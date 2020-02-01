<?php

declare(strict_types=1);

namespace App\Bridges;

use Spiral\Goridge\SocketRelay;
use Spiral\RoadRunner\PSR7Client;
use Spiral\RoadRunner\Worker;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Throwable;

class RoadRunnerBridge extends ABridge
{
    public function start(): void
    {
        $errorHandler = $this->getErrorHandler();
        $relay = new SocketRelay('/tmp/road-runner.sock', null, SocketRelay::SOCK_UNIX);
        $psr7 = new PSR7Client(new Worker($relay));
        /** @noinspection PhpDeprecationInspection */
        $dictatorsFactory = new DiactorosFactory();

        while ($req = $psr7->acceptRequest()) {
            try {
                $response = $this->handleRequest($this->castPsrRequest($req));
                $psr7->respond($dictatorsFactory->createResponse($response));
            } catch (Throwable $e) {
                $errorHandler->report($e);
                $psr7->getWorker()->error((string)$e);
            }
        }
    }
}
