<?php

namespace division\HTTP\Routing;


use division\Data\DAO\CharacterDAO;
use division\Data\DAO\CharacterTagDAO;
use division\Data\DAO\TagDAO;
use division\Models\Enums\Color;
use division\Models\Enums\Rarity;
use division\Models\Managers\CharacterManager;
use division\Models\Managers\TagManager;
use division\Models\User;
use division\Utils\Flashes;
use division\Utils\FlashMessage;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class CharacterController extends AbstractController {

	private function getCharacterList(): array {
		$characterManager = new CharacterManager(new CharacterDAO($this->database), new CharacterTagDAO($this->database));

		return $characterManager->getAllCharacters();
	}

	public function postUpdateCharacter(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$file = $request->getUploadedFiles()['Image'];

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		if (!empty($post['Id']) && !empty($post['Name']) && !empty($post['Rarity']) && !empty($post['Color'])) {
			$characterManager = new CharacterManager(new CharacterDAO($this->database), new CharacterTagDAO($this->database));

			if ($file->getClientFileName() !== '') {
				$filename = $file->getClientFileName();
				$filename = explode('.', $filename);
				$extension = array_pop($filename);
				$filename = implode('.', $filename) . '.' . $extension;

				if (in_array(strtolower($extension), ['png', 'jpg', 'webp', 'jpeg', 'svg'])) {
					$image = $post['Id'] . '.' . $extension;

					$file->moveTo(__DIR__ . '/../../../public/images/characters/' . $image);
				}
			} else {
				$post['Image'] = $post['Id'];
			}

			$old = $characterManager->getByImage($post['oldId']);

			$tagManager = new TagManager(new TagDAO($this->database));
			$tags = [];

			if (!array_key_exists('Tags', $post)) {
				$post['Tags'] = [];
			} else if (!is_array($post['Tags'])) {
				$post['Tags'] = [$post['Tags']];
			}
			foreach ($post['Tags'] as $tag) {
				$tags[] = $tagManager->getByName($tag);
			}


			if(!array_key_exists('IsLF',$post)){
				$post['IsLF'] = $old->isLF();
			}
			else if($post['IsLF'] === 'on'){
				$post['IsLF'] = true;
			}
			else if ($post['IsLF'] === 'off'){
				$post['IsLF'] = false;
			}

			unset($post['Tags']);

			$character = $characterManager->updateCharacter($post);


			if ($character !== null) {
				$characterTagDAO = new CharacterTagDAO($this->database);
				$old = $characterTagDAO->getByCharacter($character->getImage());

				foreach ($old as $tag) {
					if (!in_array($tag, $tags)) {
						$characterTagDAO->delete($character, $tag);
					}
				}
				foreach ($tags as $tag) {
					if (!in_array($tag, $old)) {
						$characterTagDAO->create($character, $tag);
					}
				}


				return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('character-list'));
			}

			Flashes::add(FlashMessage::danger("Une erreur est survenue durant la modification du personnage"));
			return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('character-list'));
		}
		Flashes::add(FlashMessage::danger('Veuillez remplir les champs obligatoires !'));
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('character-list'));
	}


	public function viewListCharacters(Request $request, Response $response, Twig $twig): Response {
		$tagDAO = new TagDAO($this->database);
		$tags = $tagDAO->getAll();
		$user = $request->getAttribute(User::class);
		return $twig->render($response, 'characters.twig', [
			'flashes' => Flashes::all(),
			'user' => $user,
			'characters' => $this->getCharacterList(),
			'rarities' => Rarity::cases(),
			'colors' => Color::cases(),
			'tags' => $tags
		]);
	}
}
