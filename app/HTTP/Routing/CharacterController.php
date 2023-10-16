<?php

namespace division\HTTP\Routing;


use division\Data\DAO\CharacterDAO;
use division\Models\Managers\CharacterManager;
use division\Models\User;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class CharacterController extends AbstractController {

	private function getCharacterList(): array {
		$characterManager = new CharacterManager(new CharacterDAO($this->database));

		$characters = $characterManager->getAllCharacters();

		return $characters;
	}

	public function __invoke(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		return $twig->render($response, 'characters.twig', [
			'user' => $user,
			'characters' => $this->getCharacterList()
		]);
	}
}
