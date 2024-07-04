<?php

namespace division\HTTP\Routing;

use division\Data\Database;
use division\Models\User;
use division\Utils\Flashes;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class IndexController extends AbstractController {
	public function __construct(Database $database) {
		parent::__construct($database);
	}

	public function viewMainPage(Response $response, Request $request, Twig $twig): Response {
		unset($_SESSION['filtres']);
		$user = $request->getAttribute(User::class);
		return $twig->render($response, 'main.twig', [
			'flashes' => Flashes::all(),
			'user_id' => @$_SESSION['a2v_user'],
			'user' => $user
		]);
	}

	public function viewNoticesPage(Response $response, Request $request, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		return $twig->render($response, 'legal_notices.twig', [
			'flashes' => Flashes::all(),
			'user' => $user
		]);
	}

	public function viewCGUPage(Response $response, Request $request, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		return $twig->render($response, 'cgu.twig', [
			'flashes' => Flashes::all(),
			'user' => $user
		]);
	}
}
