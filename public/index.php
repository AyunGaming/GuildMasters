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
use division\Utils\Flashes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Kamenews.php';
require_once __DIR__ . '/../app/Models/Article.php';
require_once __DIR__ . '/../app/Models/Enums/Rarity.php';
require_once __DIR__ . '/../app/Models/Enums/Color.php';
require_once __DIR__ . '/../app/Models/Enums/Role.php';

session_start();
ini_set('display_errors',1);

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();

try {
	$database = new Database(DatabaseConfig::load());
} catch (RuntimeException) {
	die('Cannot connect to database');
}

$container->set(Database::class, $database);

$twig = Twig::create(__DIR__ . '/../app/Templates', [
	'debug' => true,
]);

$twig->getEnvironment()->addExtension(new \Twig\Extension\DebugExtension());


$container->set(Twig::class, $twig);

$app = Bridge::create($container);
$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

$app->group('/signin', static function (RouteCollectorProxy $signIn) {
	$signIn->post('', [UserController::class, 'login']);
	$signIn->get('', UserController::class)->setName('sign-in');
});

$app->get('/signout', [UserController::class, 'signOut'])->setName('sign-out');

$app->group('/admin', static function (RouteCollectorProxy $admin) {
	$admin->group('/characters', static function (RouteCollectorProxy $characters) {
		$characters->group('/create', static function (RouteCollectorProxy $create) {
			$create->post('', [CharacterController::class, 'postCreateCharacter']);
			$create->get('', [CharacterController::class, 'viewCreateCharacter'])->setName('character-create');
		});

		$characters->post('/update-character', [CharacterController::class, 'postUpdateCharacter'])->setName('character-update');
		$characters->post('/delete-character', [CharacterController::class, 'postDeleteCharacter'])->setName('delete-character');
        $characters->post('', [CharacterController::class, 'postGetFilters'])->setName('search-filter-character');
		$characters->get('/{page}', [CharacterController::class, 'viewPagedListCharacters'])->setName('character-list');
	});
});

$app->group('/kamenews', static function (RouteCollectorProxy $kamenews) {
	$kamenews->group('/list', static function (RouteCollectorProxy $list) {
		$list->get('', [KamenewsController::class, 'displayAllKamenews'])->setName('kamenews');
	});

	$kamenews->group('/read', static function (RouteCollectorProxy $read) {
		$read->post('/get/{id:[1-9][0-9]*}', [KamenewsController::class, 'postGetKamenews'])->setName('read-kamenews');
		$read->get('', [KamenewsController::class, 'readKamenews'])->setName('display-kamenews');
	});

	$kamenews->group('/admin', static function (RouteCollectorProxy $admin){
		$admin->post('/delete-kamenews', [KamenewsController::class, 'deleteKamenews'])->setName('delete-kamenews');
		$admin->get('', [KamenewsController::class, 'displayAdminKamenews'])->setName('admin-kamenews');
	});

	$kamenews->group('/edit', static function (RouteCollectorProxy $edit){
		$edit->post('/get/{id:[1-9][0-9]*}', [KamenewsController::class, 'postEditKamenews'])->setName('get-edit-kamenews');
		$edit->post('/article', [KamenewsController::class, 'postEditArticle'])->setName('post-edit-article');
		$edit->post('/kamenews', [KamenewsController::class, 'editKamenews'])->setName('post-edit-kamenews');
		$edit->post('/remove-article', [KamenewsController::class, 'removeArticle'])->setName('remove-article');
		$edit->get('', [KamenewsController::class, 'displayEditKamenews'])->setName('edit-kamenews');
	});

	$kamenews->group('/create', static function (RouteCollectorProxy $create){
		$create->post('/article', [KamenewsController::class, 'createArticle'])->setName('create-article');
		$create->post('/kamenews', [KamenewsController::class, 'createKamenews'])->setName('create-kamenews');
		$create->post('/delete-article', [KamenewsController::class, 'deleteArticle'])->setName('delete-article');
		$create->get('', [KamenewsController::class, 'displayCreateKamenews'])->setName('new-kamenews');
	});
});

$app->get('/', static function (ServerRequestInterface $request, ResponseInterface $response, Twig $twig): ResponseInterface {
	$user = $request->getAttribute(User::class);
	$parser = RouteContext::fromRequest($request)->getRouteParser();

	return $twig->render($response, 'main.twig', [
		'flashes' => Flashes::all(),
		'user_id' => @$_SESSION['a2v_user'],
		'user' => $user
	]);
})->setName('home');

$app->addMiddleware(new GetUserMiddleware(new UserManager(new UserDAO($database))));

$app->run();
