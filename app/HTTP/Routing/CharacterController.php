<?php

namespace division\HTTP\Routing;


use division\Data\DAO\CharacterDAO;
use division\Data\DAO\CharacterTagDAO;
use division\Data\DAO\TagDAO;
use division\Data\Database;
use division\Exceptions\CannotUpdateCharacterException;
use division\Models\Character;
use division\Models\Enums\Color;
use division\Models\Enums\Rarity;
use division\Models\Managers\CharacterManager;
use division\Models\Managers\TagManager;
use division\Models\User;
use division\Utils\Flashes;
use division\Utils\FlashMessage;
use Fig\Http\Message\StatusCodeInterface;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class CharacterController extends AbstractController {
	private TagManager $tagManager;
	private CharacterManager $characterManager;

	private CharacterTagDAO $characterTagDAO;

	public function __construct(Database $database) {
		parent::__construct($database);
		$this->tagManager = new TagManager(new TagDAO($this->database));
		$this->characterManager = new CharacterManager(new CharacterDAO($this->database), new CharacterTagDAO($this->database));
		$this->characterTagDAO = new CharacterTagDAO($this->database);
	}

	private function getCharacterList(): array {
		return $this->characterManager->getAllCharacters();
	}

	private function saveImage($file, array $post): array {
		if ($file->getClientFileName() !== '') {
			$filename = $file->getClientFileName();
			$filename = explode('.', $filename);
			$extension = array_pop($filename);
			$filename = implode('.', $filename) . '.' . $extension;

			if (strtolower($extension) == 'png') {
				$image = $post['Id'] . '.' . $extension;
				file_exists(__DIR__ . '/../../../public/images/characters/' . $image) && unlink(__DIR__ . '/../../../public/images/characters/' . $image);
				$file->moveTo(__DIR__ . '/../../../public/images/characters/' . $image);
			}
		} else {
			$post['Image'] = $post['Id'];
		}
		return $post;
	}

	private function setTagsPost(array $post): array {
		$tags = [];

		if (!array_key_exists('Tags', $post)) {
			$post['Tags'] = [];
		} else if (!is_array($post['Tags'])) {
			$post['Tags'] = [$post['Tags']];
		}
		foreach ($post['Tags'] as $tag) {
			$tags[] = $this->tagManager->getByName($tag);
		}

		return $tags;
	}

	private function setIsLFPost(array $post, Character $old): array {
		if (!array_key_exists('IsLF', $post)) {
			$post['IsLF'] = $old->isLF();
		} else if ($post['IsLF'] === 'on') {
			$post['IsLF'] = true;
		} else if ($post['IsLF'] === 'off') {
			$post['IsLF'] = false;
		}

		return $post;
	}

	private function updateTags(Character $character, array $tags, array $old, CharacterTagDAO $dao): void {
		foreach ($old as $tag) {
			if (!in_array($tag, $tags)) {
				$dao->delete($character, $tag);
			}
		}
		foreach ($tags as $tag) {
			if (!in_array($tag, $old)) {
				$dao->create($character, $tag);
			}
		}
	}

	public function postCreateCharacter(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$file = $request->getUploadedFiles()['characterImage'];

		$parser = RouteContext::fromRequest($request)->getRouteParser();

		$res = $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('character-list'));

		$post = $this->saveImage($file, $post);

		if (!array_key_exists('IsLF', $post)) {
			$post['IsLF'] = false;
		}
		$tags = $this->setTagsPost($post);

		unset($post['Tags']);


		try {
			$this->characterManager->createCharacter($post);
		} catch (\RuntimeException $e) {
			var_dump("Exception 1");
			Flashes::add(FlashMessage::danger($e->getMessage()));
		}

		try {
			$character = $this->characterManager->getByImage($post['Id']);
			foreach ($tags as $tag) {
				$this->characterTagDAO->create($character, $tag);
			}
			Flashes::add(FlashMessage::success("Le personnage {$post['Id']} a été créé !"));
		} catch (\RuntimeException $e) {
			var_dump("Exception 2");
			Flashes::add(FlashMessage::danger($e->getMessage()));
		}

		return $res;
	}

	public function postUpdateCharacter(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$file = $request->getUploadedFiles()['Image'];

		$parser = RouteContext::fromRequest($request)->getRouteParser();

		$res = $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('character-list'));

		$post = $this->saveImage($file, $post);

		$old = $this->characterManager->getByImage($post['oldId']);

		$post = $this->setIsLFPost($post, $old);
		$tags = $this->setTagsPost($post);

		unset($post['Tags']);

		try {
			$this->characterManager->updateCharacter($post);
		} catch (CannotUpdateCharacterException $e) {
			Flashes::add(FlashMessage::danger($e->getMessage()));
		}


		try {
			$character = $this->characterManager->getByImage($post['Id']);
			$old = $this->characterTagDAO->getByCharacter($character->getImage());
			$this->updateTags($character, $tags, $old, $this->characterTagDAO);
		} catch (PDOException) {
			Flashes::add(FlashMessage::danger("Le personnage {$post['Id']} n'existe pas"));
		}

		return $res;
	}

	public function postDeleteCharacter(Request $request, Response $response): Response {
		$post = $request->getParsedBody();

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		$res = $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('character-list'));
		$dao = new CharacterDAO($this->database);

		try {
			$character = $this->characterManager->getByImage($post['ID']);
			$dao->delete($character);
			Flashes::add(FlashMessage::success("Le personnage {$post['ID']} a été supprimé !"));
		} catch (\Exception) {
			Flashes::add(FlashMessage::danger("Le personnage {$post['ID']} n'existe pas !"));
		}

		return $res;
	}

	public function viewListCharacters(Request $request, Response $response, Twig $twig): Response {
		$tags = $this->tagManager->getAllTags();
		$characters = $this->getCharacterList();
		$user = $request->getAttribute(User::class);
		return $twig->render($response, 'characters.twig', [
			'flashes' => Flashes::all(),
			'user' => $user,
			'characters' => $characters,
			'rarities' => Rarity::cases(),
			'colors' => Color::cases(),
			'tags' => $tags
		]);
	}

	public function viewCreateCharacter(Request $request, Response $response, Twig $twig): Response {
		$tags = $this->tagManager->getAllTags();
		$characters = $this->getCharacterList();
		$user = $request->getAttribute(User::class);
		return $twig->render($response, 'charactersCreate.twig', [
			'flashes' => Flashes::all(),
			'user' => $user,
			'characters' => $characters,
			'rarities' => Rarity::cases(),
			'colors' => Color::cases(),
			'tags' => $tags
		]);
	}
}
