<?php

use DI\Bridge\Slim\Bridge;
use DI\Container;
use Psr\Http\Message\ResponseInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();

$app = Bridge::create($container);

$app->get('/', static function (ResponseInterface $response): ResponseInterface {
$response->getBody()->write('<h1>Hello World</h1>');
return $response->withHeader('Content-Type', 'text/html');
});

$app->run();