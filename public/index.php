<?php

use DI\Bridge\Slim\Bridge;
use DI\Container;
use division\Configs\DatabaseConfig;
use division\Data\DAO\UserDAO;
use division\Data\Database;
use division\HTTP\Middlewares\GetUserMiddleware;
use division\HTTP\Routing\CharacterController;
use division\HTTP\Routing\KamenewsController;
use division\HTTP\Routing\UserController;
use division\Models\Managers\UserManager;
use division\Models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use \Slim\Views\TwigMiddleware;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();

try {
	$database = new Database(DatabaseConfig::load());
} catch (RuntimeException) {
	die('Cannot connect to database');
}

$container->set(Database::class, $database);

$twig = Twig::create(__DIR__ . '/../app/templates');
$container->set(Twig::class, $twig);

$app = Bridge::create($container);
$app->add(TwigMiddleware::createFromContainer($app, Twig::class));


$app->group('/signin', static function(RouteCollectorProxy $signIn){
	$signIn->post('', [UserController::class, 'login']);
	$signIn->get('', UserController::class)->setName('sign-in');
});

$app->get('/signout', [UserController::class, 'signOut'])->setName('sign-out');

$app->get('/characters', CharacterController::class)->setName('character-list');

$app->get('/kamenews', KamenewsController::class)->setName('kamenews');

$app->get('/', static function (ServerRequestInterface $request, ResponseInterface $response, Twig $twig): ResponseInterface {
	$user = $request->getAttribute(User::class);
	return $twig->render($response, 'kamenewsAdmin.twig', [
		'user_id' => @$_SESSION['a2v_user'],
		'user' => $user
	]);
})->setName('home');

$app->addMiddleware(new GetUserMiddleware(new UserManager(new UserDAO($database))));

$app->run();
