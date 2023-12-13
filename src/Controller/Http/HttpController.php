<?php

namespace Fbartz\BrowserGames\Controller\Http;

use Fbartz\BrowserGames\Controller\Http\Objects\User;
use Fbartz\BrowserGames\Repository\UserRepository;
use Fbartz\BrowserGames\View\Page\Router;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

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
                $routes = explode("/",$path);

                if($request->getMethod() !== "GET"){
                    $body = json_decode($request->getBody(),true);
                }

                switch ($routes[2]) {
                    case "user":
                        $promise = User::readRequest(UserRepository::getInstance(), implode("/", array_slice($routes, 3)), $body ?? $request->getQueryParams(), self::$CORS_HEADERS);
                        break;
                    default:
                        return new Response(400, self::$CORS_HEADERS);
                }
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
                Router::route((int)($request->getQueryParams()['page'] ?? 0));
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

            if($promise instanceof Response){
                return $promise;
            }else{
                return $promise->then(function ($results){
                    var_dump($results);
                    if (!is_array($results) || !isset($results)) {
                        return new Response(200, self::$CORS_HEADERS);
                    }
                    if (isset($results[1])) {
                        return new Response($results[0], $this->enhanceHeaders(['Content-Type' => 'application/json']),
                            json_encode($results[1]));
                    }
                    return new Response($results[0], self::$CORS_HEADERS);
                });
            }

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