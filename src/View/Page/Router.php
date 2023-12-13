<?php

namespace Fbartz\BrowserGames\View\Page;

class Router
{

    public static function route(int $page = 0): void
    {
        switch ($page) {
            case 1:
                include("Start/login.html");
                break;
            case 2:
                include ("Adminboard/index.html");
                break;
            default:
                include("Start/index.html");
                break;
        }
    }

}