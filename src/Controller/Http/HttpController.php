<?php

namespace Fbartz\BrowserGames\Controller\Http;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;

class HttpController
{

    private Httpserver $server;

    public function __construct($loop, $websocket)
    {

        $httpRequestHandler = function (ServerRequestInterface $request){

        };

        $this->server = new HttpServer($loop, $websocket, $httpRequestHandler);
        $this->server->on('error', function (\Throwable $exception) {
            echo 'ERROR: ' . $exception . PHP_EOL;
        });
    }

    public function getServer():HttpServer
    {
        return $this->server;
    }

}