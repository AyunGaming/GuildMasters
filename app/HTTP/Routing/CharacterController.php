<?php

namespace division\HTTP\Routing;


use division\Data\DAO\character\CharacterDAO;
use division\Models\Managers\CharacterManager;
use division\Models\User;
use division\Utils\Flashes;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CharacterController extends AbstractController {

	private function getCharacterList(): array {
		$characterManager = new CharacterManager(new CharacterDAO($this->database));

		return $characterManager->getAllCharacters();
	}

	public function __invoke(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		return $twig->render($response, 'characters.twig', [
			'flashes' => Flashes::all(),
			'user' => $user,
			'characters' => $this->getCharacterList()
		]);
	}
}
