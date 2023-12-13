<?php

namespace Fbartz\BrowserGames\Controller\Http\Objects;

use Fbartz\BrowserGames\Repository\BaseRepository;
use React\Http\Message\Response;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

class User
{
    use RequestObject;

    public static function readRequest(BaseRepository $repository, string $path, array $params,
                                       array          $headers): Promise|PromiseInterface
    {

        switch ($path) {
            case "login":
                return new Promise(function ($resolve) use ($params, $repository) {
                    return $repository->findBy(["Username" => $params['Username'], "Password" => $params['Password']])
                        ->then(function ($result) use ($resolve, $repository) {
                            if ($result !== []) {
                                $result = $result[0];
                                $valid = new \DateTime($result['validTill']);
                                $today = new \DateTime();

                                if ($valid <= $today) {
                                    $resolve([200, ["key" => $result['token']]]);
                                }else{
                                    $token = self::genToken();
                                    $repository->update(["token" => $token], $result['ID'])->then(function () use ($resolve, $result) {
                                        $resolve([200, ["key" => $result['token']]]);

                                    });
                                }
                            }else{
                                $resolve([401]);
                            }
                        });
                });
        }
    }

    private static function genToken(): string
    {
        return substr(bin2hex(base64_encode(random_bytes(8) . time())), 0, 16);
    }

}