<?php

namespace Fbartz\BrowserGames\View\Page;

class Router
{

    public static function loadPage(int $page = 1): void
    {
        switch ($page) {
            case 1:
                include_once("index.html");
                break;
            case 2:
                include_once ("login.html");
        }
    }

}