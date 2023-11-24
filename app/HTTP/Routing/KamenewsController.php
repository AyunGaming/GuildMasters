<?php

namespace division\HTTP\Routing;

use division\Data\DAO\KamenewsDAO;
use division\Models\Managers\KamenewsManager;
use division\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class KamenewsController extends AbstractController {

	public function getAllKamenews(): array {
		$kamenewsManager = new KamenewsManager(new KamenewsDAO($this->database));

		return $kamenewsManager->getAllKamenews();
	}

	public function __invoke(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		$kamenews = $this->getAllKamenews();
		return $twig->render($response, 'kamenews.twig', [
			'user' => $user,
			'kamenews' => array_reverse($kamenews)
		]);
	}
}