<?php


namespace Fbartz\BrowserGames\Controller\Http\Objects;

use Fbartz\BrowserGames\Repository\BaseRepository;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

trait RequestObject
{

    abstract public static function readRequest(BaseRepository $repository, string $path, array $params,
                                                array          $headers): Promise|PromiseInterface;

}