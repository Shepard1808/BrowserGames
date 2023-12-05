<?php

namespace Fbartz\BrowserGames\Controller\Http;

use Fbartz\BrowserGames\View\Page\Router;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;

class HttpController
{

    private Httpserver $server;
    private static array $CORS_HEADERS = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Headers' => '*',
        'Access-Control-Allow-Methods' => '*',
        'Connection' => 'Keep-Alive'
    ];

    public function __construct($loop, $websocket)
    {

        $httpRequestHandler = function (ServerRequestInterface $request) {


            if ($request->getMethod() === 'OPTIONS') {
                return new Response(200, self::$CORS_HEADERS);
            }

            $promise = null;

            $path = $request->getUri()->getPath();

            if (str_starts_with($path, "/api")) {

            } else if ($path === "/" || str_starts_with($path, "/View")) {
                $type = "";
                if ($request->getMethod() === "GET") {
                    $type = substr($request->getRequestTarget(), -4);

                    if (strpos($type, ".ini") !== false || strpos($type, ".log") !== false) {
                        return new Response(403, self::$CORS_HEADERS);
                    }

                    if (strpos($type, "js") !== false) {
                        $type = "text/javascript";
                    } else if (strpos($type, "css") !== false) {
                        $type = "text/css";
                    } else if (strpos($type, "svg") !== false) {
                        $type = "image/svg+xml";
                    } else if ($type === "/") {
                        $type = "text/html";
                    }
                }

                ob_start();
                require_once './View/Page/Router.php';
                Router::route((int)($request->getQueryParams()['page'] ?? 1));
                $content = ob_get_clean();

                if ($type === "text/javascript" || $type === "text/css" || $type === "image/svg+xml") {

                    $path = realpath(__DIR__ . "/../../" . $request->getRequestTarget());
                    $content = file_get_contents($path);
                    if ($content === false) {
                        $content = file_get_contents("../../.." . $request->getRequestTarget());
                    }
                }

                return new Response(200, $this->enhanceHeaders(["Content-Type" => $type]), $content);

            }else{
                return new Response(403, self::$CORS_HEADERS);
            }

            $promise->then(function ($results){
                if (!is_array($results) || !isset($results)) {
                    return new Response(200, self::$CORS_HEADERS);
                }
                if (isset($results[1])) {
                    return new Response($results[0], $this->enhanceHeaders(['Content-Type' => 'application/json']),
                        json_encode($results[1]));
                }
                return new Response($results[0], self::$CORS_HEADERS);
            });

        };

        $this->server = new HttpServer($loop, $websocket, $httpRequestHandler);
        $this->server->on('error', function (\Throwable $exception) {
            echo 'ERROR: ' . $exception . PHP_EOL;
        });
    }

    public function getServer(): HttpServer
    {
        return $this->server;
    }

    private function enhanceHeaders(array $headers): array
    {
        return array_merge($headers, self::$CORS_HEADERS);
    }

}