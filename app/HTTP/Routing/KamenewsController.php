<?php

namespace division\HTTP\Routing;

use division\Data\DAO\ArticlesDAO;
use division\Data\DAO\KamenewsArticlesDAO;
use division\Data\DAO\KamenewsDAO;
use division\Data\DAO\UserDAO;
use division\Data\Database;
use division\Models\Managers\KamenewsManager;
use division\Models\User;
use division\Utils\Alerts;
use division\Utils\alerts\Alert;
use division\Utils\alerts\AlertTypes;
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
			Alerts::add(new Alert("Le kamenews n°{$post['id']} a bien été modifié", AlertTypes::SUCCESS));
		} catch (\Exception) {
			Alerts::add(new Alert("Le kamenews n°{$post['id']} n'a pas pu être modifié", AlertTypes::ERROR));
		}

		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('admin-kamenews'));
	}

	public function editKamenews(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$parser = RouteContext::fromRequest($request)->getRouteParser();

		try {
			$this->kamenewsManager->updateKamenews($post);
			Alerts::add(new Alert("L'article a bien été modifié", AlertTypes::SUCCESS));
		} catch (\Exception) {
			Alerts::add(new Alert("L'article n'a pas pu être modifié", AlertTypes::ERROR));
		}

		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('admin-kamenews'));
	}

	public function deleteArticle(Request $request, Response $response): Response {
		$post = $request->getParsedBody();

		try {
			$this->kamenewsManager->deleteArticle($post['id']);
			Alerts::add(new Alert("L'article n°{$post['id']} a bien été supprimé", AlertTypes::SUCCESS));
		} catch (\Exception) {
			Alerts::add(new Alert("L'article n°{$post['id']} n'a pas pu être supprimé", AlertTypes::ERROR));
		}

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('new-kamenews'));
	}

	public function removeArticle(Request $request, Response $response): Response {
		$post = $request->getParsedBody();

		try {
			$this->kamenewsManager->deleteArticle($post['id']);
			Alerts::add(new Alert("L'article n°{$post['id']} a bien été supprimé", AlertTypes::SUCCESS));
		} catch (\Exception) {
			Alerts::add(new Alert("L'article n°{$post['id']} n'a pas pu être supprimé", AlertTypes::ERROR));
		}

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('edit-kamenews'));
	}

	public function createKamenews(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$file = $request->getUploadedFiles()['banner'];
		if ($file->getClientFileName() !== '') {
			$filename = $file->getClientFileName();
			$filename = explode('.', $filename);
			$extension = array_pop($filename);
			$filename = implode('.', $filename) . '.' . $extension;

			file_exists(__DIR__ . '/../../../public/images/kamenews/' . $filename) && unlink(__DIR__ . '/../../../public/images/kamenews/' . $filename);
			$file->moveTo(__DIR__ . '/../../../public/images/kamenews/' . $filename);
			$image = $filename;
		}
		if (isset($image)) $post['banner'] = $image;

		$parser = RouteContext::fromRequest($request)->getRouteParser();

		try {
			$this->kamenewsManager->addKamenews($post);
			Alerts::add(new Alert("Le kamenews a bien été créé", AlertTypes::SUCCESS));
		} catch (\Exception) {
			Alerts::add(new Alert("Le kamenews n'a pas pu être créé", AlertTypes::ERROR));
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
			Alerts::add(new Alert("Le kamenews n°{$post['ID']} a été supprimé", AlertTypes::SUCCESS));
		} catch (\Exception $e) {
			Alerts::add(new Alert("Le kamenews n°{$post['ID']} n'a pas pu être supprimé", AlertTypes::ERROR));
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
			Alerts::add(new Alert("L'article a bien été créé", AlertTypes::SUCCESS));
		} catch (\Exception) {
			Alerts::add(new Alert("L'article n'a pas pu être créé", AlertTypes::ERROR));
		}

		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('new-kamenews'));
	}

	//region Display
	public function readKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		$parser = RouteContext::fromRequest($request)->getRouteParser();

		try {
			return $twig->render($response, 'kamenewsViewer.twig', [
				'alerts' => Alerts::all(),
				'user' => $user,
				'kamenews' => $_SESSION['display_kamenews'],
			]);
		} catch (\Exception $e) {
			Alerts::add(new Alert($e->getMessage(), AlertTypes::ERROR));
			return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('home'));
		}
	}

	public function displayLastKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);

		return $twig->render($response, 'kamenewsViewer.twig', [
			'user' => $user,
			'kamenews' => $this->kamenewsManager->getLastKamenews(),
		]);
	}

	public function displayAllKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		$kamenews = $this->getAllKamenews();

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $twig->render($response, 'kamenews.twig', [
			'alerts' => Alerts::all(),
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
			'alerts' => Alerts::all(),
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
			'alerts' => Alerts::all(),
			'kamenews' => @$_SESSION['display_kamenews'],
			'user_id' => @$_SESSION['a2v_user'],
			'user' => $user
		]);
	}

	public function displayCreateKamenews(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);

		return $twig->render($response, 'kamenewsCreate.twig', [
			'alerts' => Alerts::all(),
			'user_id' => @$_SESSION['a2v_user'],
			'user' => $user
		]);
	}
	//endregion
}