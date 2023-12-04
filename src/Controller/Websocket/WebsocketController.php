<?php

namespace Fbartz\BrowserGames\Controller\Websocket;

use Ratchet\RFC6455\Messaging\Message;
use Voryx\WebSocketMiddleware\WebSocketConnection;
use Voryx\WebSocketMiddleware\WebSocketMiddleware;

class WebsocketController
{

    private WebSocketMiddleware $middleware;


    public function __construct()
    {

        $this->middleware = new WebSocketMiddleware([],function (WebSocketConnection $connection){

            $connection->on("message", function (Message $message){

            });

            $connection->on("close", function (){

            });

        });

    }

    public function getMiddleware(): WebSocketMiddleware
    {
        return $this->middleware;
    }
}