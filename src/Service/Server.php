<?php

namespace Fbartz\BrowserGames\Service;

use Fbartz\BrowserGames\Controller\Http\HttpController;
use Fbartz\BrowserGames\Controller\Websocket\WebsocketController;
use React\EventLoop\Loop;
use React\MySQL\Factory;
use React\Socket\SocketServer;

class Server
{

    private HttpController $httpController;
    private WebsocketController $websocketController;

    public function __construct()
    {

        $loop = Loop::get();
        while ($loop === null){
            $loop = Loop::get();
        }

        $this->websocketController = new WebsocketController();
        $this->httpController = new HttpController($loop,$this->websocketController->getMiddleware());

        $this->httpController->getServer()->listen(new SocketServer("0.0.0.0:55498"));
        $loop->run();

    }


}