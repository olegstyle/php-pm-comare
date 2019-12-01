<?php

declare(strict_types=1);

namespace App\Bridges;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response as ReactResponse;
use React\Http\Server;
use React\Socket\Server as ReactServer;
use Symfony\Component\HttpFoundation\Request;

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
                $request = $this->castRequest($reactRequest);
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

    public function castRequest(ServerRequestInterface $request): Request
    {
        $method  = $request->getMethod();
        $headers = $request->getHeaders();
        $content = $request->getBody();
        $post    = [];
        if (isset($headers['Content-Type']) &&
            strpos($headers['Content-Type'], 'application/x-www-form-urlencoded') === 0 &&
            in_array(strtoupper($method), ['POST', 'PUT', 'DELETE', 'PATCH'])
        ) {
            parse_str($content, $post);
        }
        $sfRequest = new Request(
            $request->getQueryParams(),
            $post,
            [],
            $request->getCookieParams(),
            $request->getUploadedFiles(),
            [],
            $content
        );
        $sfRequest->setMethod($method);
        $sfRequest->headers->replace($headers);
        $sfRequest->server->set('REQUEST_URI', (string) $request->getUri());
        if (isset($headers['Host'])) {
            $sfRequest->server->set('SERVER_NAME', current($headers['Host']));
        }

        return $sfRequest;
    }
}
