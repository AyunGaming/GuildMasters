<?php

use DI\Bridge\Slim\Bridge;
use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use \Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();


$twig = Twig::create(__DIR__ . '/../app/templates');
$container->set(Twig::class, $twig);

$app = Bridge::create($container);
$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

$app->get('/', static function (ResponseInterface $response, Twig $twig): ResponseInterface {
	return $twig->render($response, 'base.twig');
})->setName('home');

$app->run();