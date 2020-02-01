<?php

declare(strict_types=1);

namespace App\Bridges;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response as ReactResponse;
use React\Http\Server;
use React\Socket\Server as ReactServer;

class ReactBridge extends ABridge
{
    public function start(): void
    {
        $loop = Factory::create();
        $logger = $this->getLogger();
        $logger->info('selected_event_loop', ['event_loop' => get_class($loop)]);
        $errorHandler = $this->getErrorHandler();
        $server = new Server(
            function (ServerRequestInterface $reactRequest): ReactResponse {
                $request = $this->castPsrRequest($reactRequest);
                $response = $this->handleRequest($request);

                return new ReactResponse(
                    $response->getStatusCode(),
                    $response->headers->all(),
                    $response->getContent()
                );
            }
        );
        $server->on(
            'error',
            static function (Exception $e) use ($errorHandler) {
                $errorHandler->report($e);
            }
        );

        $socket = new ReactServer('tcp://0.0.0.0:9000', $loop);
        $server->listen($socket);
        $logger->info('Server running', ['addr' => 'tcp://0.0.0.0:9000']);
        $loop->run();
    }
}
