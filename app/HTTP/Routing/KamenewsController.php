<?php

namespace division\HTTP\Routing;

use division\Data\DAO\ArticlesDAO;
use division\Data\DAO\KamenewsArticlesDAO;
use division\Data\DAO\KamenewsDAO;
use division\Data\DAO\UserDAO;
use division\Data\Database;
use division\Models\Managers\KamenewsManager;
use division\Models\User;
use division\Utils\Flashes;
use division\Utils\FlashMessage;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;


class KamenewsController extends AbstractController {
	private KamenewsManager $kamenewsManager;

	public function __construct(Database $database) {
		parent::__construct($database);
		$kamenewsDAO = new KamenewsDAO($this->database, new UserDAO($this->database));
		$articlesDAO = new ArticlesDAO($this->database);
		$this->kamenewsManager = new KamenewsManager($kamenewsDAO, $articlesDAO, new KamenewsArticlesDAO($this->database, $kamenewsDAO, $articlesDAO));
	}

	public function getAllKamenews(): array {
		return $this->kamenewsManager->getAllKamenews();
	}

	public function postGetKamenews(int $id, Request $request, Response $response): Response {
		$_SESSION["display_kamenews"] = $this->kamenewsManager->getKamenews($id);

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('display-kamenews'));
	}

	public function deleteKamenews(Request $request, Response $response): Response {
		$post = $request->getParsedBody();

		try {
			$this->kamenewsManager->deleteKamenews($post['ID']);
			Flashes::add(FlashMessage::danger("Le kamenews n°{$post['ID']} n'a pas pu être supprimé"));
		} catch (\Exception $e) {
			Flashes::add(FlashMessage::danger("Le kamenews n°{$post['ID']} n'a pas pu être supprimé"));
		}

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('admin-kamenews'));
	}

	public function readKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		$parser = RouteContext::fromRequest($request)->getRouteParser();

		try {
			return $twig->render($response, 'kamenewsViewer.twig', [
				'user' => $user,
				'kamenews' => $_SESSION['display_kamenews'],
			]);
		} catch (\Exception) {
			return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND)->withHeader('Location', $parser->urlFor('home'));
		}
	}

	public function displayAllKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		$kamenews = $this->getAllKamenews();

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $twig->render($response, 'kamenews.twig', [
			'read_kamenews_url' => $parser->urlFor('read-kamenews', [
				'id' => 'Id'
			]),
			'user' => $user,
			'kamenews' => array_reverse($kamenews)
		]);
	}

	public function displayAdminKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		$kamenews = $this->getAllKamenews();

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $twig->render($response, 'kamenewsAdmin.twig', [
			'read_kamenews_url' => $parser->urlFor('read-kamenews', [
				'id' => 'Id'
			]),
			'user' => $user,
			'kamenews' => $kamenews
		]);
	}
}