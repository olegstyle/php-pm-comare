<?php

declare(strict_types=1);

namespace App\Bridges;

use DateTime;
use Exception;
use Illuminate\Http\Request as IlluminateRequest;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\HTTP\Server;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

class SwooleBridge extends ABridge
{
    public const CHUNK_SIZE = 8192;

    public function start(): void
    {
        $server = new Server('0.0.0.0', 9000);
        $errorHandler = $this->getErrorHandler();

        $server->on(
            'request',
            function (Request $req, Response $res) use ($errorHandler): void {
                try {
                    $this->castResponse(
                        $res,
                        $this->handleRequest($this->castSwooleRequest($req))
                    );
                } catch (Exception $e) {
                    $errorHandler->report($e);
                    try {
                        $request = $this->castSwooleRequest($req);
                    } catch (Throwable $e2) {
                        $request = new IlluminateRequest();
                    }
                    $this->castResponse($res, $errorHandler->render($request, $e));
                }
            }
        );

        $server->start();
    }

    protected function castSwooleRequest(Request $req): SymfonyRequest
    {
        $request = new SymfonyRequest(
            $req->get ?? [],
            $req->post ?? [],
            [],
            $req->cookie ?? [],
            $req->files ?? [],
            $this->transformServerParameters($req->server ?? [], $req->header ?? []),
            $req->rawContent()
        );
        if (0 === strpos((string)$request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), ['PUT', 'DELETE', 'PATCH'])
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBag($data);
        }

        return $request;
    }

    protected function transformServerParameters(array $server, array $header): array
    {
        $__SERVER = [];
        foreach ($server as $key => $value) {
            $key = strtoupper($key);
            $__SERVER[$key] = $value;
        }

        foreach ($header as $key => $value) {
            $key = str_replace('-', '_', $key);
            $key = strtoupper($key);

            if (! in_array($key, ['REMOTE_ADDR', 'SERVER_PORT', 'HTTPS'])) {
                $key = 'HTTP_' . $key;
            }

            $__SERVER[$key] = $value;
        }

        return $__SERVER;
    }

    protected function castResponse(Response $swooleResponse, SymfonyResponse $symfonyResponse): void
    {
        /* RFC2616 - 14.18 says all Responses need to have a Date */
        if (! $symfonyResponse->headers->has('Date')) {
            $symfonyResponse->setDate(DateTime::createFromFormat('U', time()));
        }

        // headers
        $headers = $symfonyResponse->headers->allPreserveCase();
        if (isset($headers['Set-Cookie'])) {
            unset($headers['Set-Cookie']);
        }
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                $swooleResponse->header($name, (string) $value);
            }
        }

        // status
        $swooleResponse->status($symfonyResponse->getStatusCode());

        // cookies
        foreach ($symfonyResponse->headers->getCookies() as $cookie) {
            $method = $cookie->isRaw() ? 'rawcookie' : 'cookie';
            $swooleResponse->$method(
                $cookie->getName(),
                (string) $cookie->getValue(),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                (string) $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
        }

        // send content in chunk
        $content = $symfonyResponse->getContent();
        if (strlen($content) <= static::CHUNK_SIZE) {
            $swooleResponse->end($content);
            return;
        }

        foreach (str_split($content, static::CHUNK_SIZE) as $chunk) {
            $swooleResponse->write($chunk);
        }

        $swooleResponse->end();
    }
}
