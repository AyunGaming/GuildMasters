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
		$userDAO = new UserDAO($this->database);
		$this->kamenewsManager = new KamenewsManager($kamenewsDAO, $articlesDAO, new KamenewsArticlesDAO($this->database, $kamenewsDAO, $articlesDAO), $userDAO);
	}

	public function postEditArticle(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$parser = RouteContext::fromRequest($request)->getRouteParser();
		$post["id"] = (int)$post["id"];

		try {
			$this->kamenewsManager->updateArticle($post);
			Flashes::add(FlashMessage::success("Le kamenews n°{$post['id']} a bien été modifié"));
		} catch (\Exception) {
			Flashes::add(FlashMessage::danger("Le kamenews n°{$post['id']} n'a pas pu être modifié"));
		}

		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('admin-kamenews'));
	}

	public function editKamenews(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$parser = RouteContext::fromRequest($request)->getRouteParser();

		try {
			$this->kamenewsManager->updateKamenews($post);
			Flashes::add(FlashMessage::success("L'article a bien été modifié"));
		} catch (\Exception) {
			Flashes::add(FlashMessage::danger("L'article n'a pas pu être modifié"));
		}

		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('admin-kamenews'));
	}

	public function deleteArticle(Request $request, Response $response): Response {
		$post = $request->getParsedBody();

		try {
			$this->kamenewsManager->deleteArticle($post['id']);
			Flashes::add(FlashMessage::success("L'article n°{$post['id']} a bien été supprimé"));
		} catch (\Exception) {
			Flashes::add(FlashMessage::danger("L'article n°{$post['id']} n'a pas pu être supprimé"));
		}

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('new-kamenews'));
	}

	public function removeArticle(Request $request, Response $response): Response {
		$post = $request->getParsedBody();

		try {
			$this->kamenewsManager->deleteArticle($post['id']);
			Flashes::add(FlashMessage::success("L'article n°{$post['id']} a bien été supprimé"));
		} catch (\Exception) {
			Flashes::add(FlashMessage::danger("L'article n°{$post['id']} n'a pas pu être supprimé"));
		}

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('edit-kamenews'));
	}

	public function createKamenews(Request $request, Response $response): Response {
		$post = $request->getParsedBody();

		$parser = RouteContext::fromRequest($request)->getRouteParser();

		try {
			$this->kamenewsManager->addKamenews($post);
			Flashes::add(FlashMessage::success("Le kamenews a bien été créé"));
		} catch (\Exception) {
			Flashes::add(FlashMessage::danger("Le kamenews n'a pas pu être créé"));
		}

		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('kamenews'));

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
			Flashes::add(FlashMessage::success("Le kamenews n°{$post['ID']} a été supprimé"));
		} catch (\Exception $e) {
			Flashes::add(FlashMessage::danger("Le kamenews n°{$post['ID']} n'a pas pu être supprimé"));
		}

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('admin-kamenews'));
	}

	public function postEditKamenews(int $id, Request $request, Response $response): Response {
		$_SESSION["display_kamenews"] = $this->kamenewsManager->getKamenews($id);
		$parser = RouteContext::fromRequest($request)->getRouteParser();

		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('edit-kamenews'));
	}

	private function saveImage($files, array $post): array {
		$images = [];
		$i = 0;
		foreach ($files as $file) {
			$i += 1;
			if ($file->getClientFileName() !== '') {
				$filename = $file->getClientFileName();
				$filename = explode('.', $filename);
				$extension = array_pop($filename);
				$filename = implode('.', $filename) . '.' . $extension;
				$title = str_replace(' ', '_', $post['title']) . "_" . $i;

				$name = "$title.$extension";
				$images[] = $name;
				file_exists(__DIR__ . '/../../../public/images/kamenews/' . $name) && unlink(__DIR__ . '/../../../public/images/kamenews/' . $name);
				$file->moveTo(__DIR__ . '/../../../public/images/kamenews/' . $name);
			}
		}

		return $images;
	}

	public function createArticle(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$file = $request->getUploadedFiles()['image'];
		$image = $this->saveImage($file, $post);
		$post['image'] = $image;

		$parser = RouteContext::fromRequest($request)->getRouteParser();

		try {
			$this->kamenewsManager->addArticle($post);
			Flashes::add(FlashMessage::success("L'article a bien été créé"));
		} catch (\Exception) {
			Flashes::add(FlashMessage::danger("L'article n'a pas pu être créé"));
		}

		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('new-kamenews'));
	}

	//region Display
	public function readKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		$parser = RouteContext::fromRequest($request)->getRouteParser();

		try {
			return $twig->render($response, 'kamenewsViewer.twig', [
				'flashes' => Flashes::all(),
				'user' => $user,
				'kamenews' => $_SESSION['display_kamenews'],
			]);
		} catch (\Exception $e) {
			Flashes::add(FlashMessage::danger($e->getMessage()));
			return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('home'));
		}
	}

	public function displayAllKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		$kamenews = $this->getAllKamenews();

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $twig->render($response, 'kamenews.twig', [
			'flashes' => Flashes::all(),
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
			'flashes' => Flashes::all(),
			'read_kamenews_url' => $parser->urlFor('read-kamenews', [
				'id' => 'Id'
			]),
			'display_edit_kamenews_url' => $parser->urlFor('get-edit-kamenews', [
				'id' => 'Id'
			]),
			'user' => $user,
			'kamenews' => $kamenews
		]);
	}

	public function displayEditKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);

		return $twig->render($response, 'kamenewsEdit.twig', [
			'flashes' => Flashes::all(),
			'kamenews' => @$_SESSION['display_kamenews'],
			'user_id' => @$_SESSION['a2v_user'],
			'user' => $user
		]);
	}

	public function displayCreateKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);

		return $twig->render($response, 'kamenewsCreate.twig', [
			'flashes' => Flashes::all(),
			'user_id' => @$_SESSION['a2v_user'],
			'user' => $user
		]);
	}
	//endregion
}