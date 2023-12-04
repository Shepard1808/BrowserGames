<?php

namespace Fbartz\BrowserGames\View\Page;

class Router
{

    public static function route(int $page = 1): void
    {
        switch ($page) {
            case 1:
                include_once("Start/index.html");
                break;
            case 2:
                include_once ("Start/login.html");
                break;
        }
    }

}