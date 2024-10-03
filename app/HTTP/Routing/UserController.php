<?php

namespace division\HTTP\Routing;

use division\Data\DAO\UserDAO;
use division\Data\Database;
use division\Models\Managers\UserManager;
use division\Models\User;
use division\Utils\Flashes;
use division\Utils\FlashMessage;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class UserController extends AbstractController {
	public function signup(Request $request, Response $response): Response {
		$post = $request->getParsedBody();

		$parser = RouteContext::fromRequest($request)->getRouteParser();

		if (!empty($post['login']) && !empty($post['password']) && !empty($post['confirm-password'])) {
			$login = htmlspecialchars($post['login']);
			$password = htmlspecialchars($post['password']);
			$confirmPassword = htmlspecialchars($post['confirm-password']);

			if ($password == $confirmPassword) {
				// Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character
				if (!preg_match("^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$^", $password)) {
					Flashes::add(FlashMessage::danger("Le mot de passe doit faire minimum 8 caractères, 
					contenir au moins 1 majuscule, une minuscule, un nombre et un caractère spécial"));
					return $response->withStatus(StatusCodeInterface::STATUS_FOUND)
						->withHeader("Location", $parser->urlFor('sign-in'));
				}

				$manager = new UserManager(new UserDAO($this->database));
				$user = $manager->register($login, $password);

				if ($user !== null) {
					$_SESSION['user_id'] = $user->getId();
					Flashes::add(FlashMessage::success("Votre compte a bien été créé"));
					return $response->withStatus(StatusCodeInterface::STATUS_FOUND)
						->withHeader('Location', $parser->urlFor('home'));
				}
			}

			Flashes::add(FlashMessage::danger('Mot de passe différent de la confirmation !'));
			return $response->withStatus(StatusCodeInterface::STATUS_FOUND)
				->withHeader('Location', $parser->urlFor('sign-in'));
		}

		return $response->withStatus(StatusCodeInterface::STATUS_FOUND);
	}

	public function login(Request $request, Response $response): Response {
		$post = $request->getParsedBody();

		if (!empty($post['login']) && !empty($post['password'])) {
			$login = htmlspecialchars($post['login']);
			$passwd = htmlspecialchars($post['password']);
			$manager = new UserManager(new UserDAO($this->database));

			$parser = RouteContext::fromRequest($request)->getRouteParser();
			$user = $manager->checkLogin($login, $passwd);

			if ($user !== null) {
				$_SESSION['user_id'] = $user->getId();
				return $response->withStatus(StatusCodeInterface::STATUS_FOUND)
					->withHeader('Location', $parser->urlFor('home'));
			}

			// Invalid login or credentials, alert the user
			Flashes::add(FlashMessage::danger('Login ou mot de passe incorrects !'));
			return $response->withStatus(StatusCodeInterface::STATUS_NOT_ACCEPTABLE)
				->withHeader('Location', $parser->urlFor('sign-in'));
		}
		return $response;
	}

	public function signOut(Request $request, Response $response): Response {
		unset($_SESSION['user_id']);
		unset($_SESSION);

		$parser = RouteContext::fromRequest($request)->getRouteParser();

		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('home'));
	}

	public function __invoke(ServerRequestInterface $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		return $twig->render($response, 'connexion.twig', [
			'flashes' => Flashes::all(),
			'user' => $user
		]);
	}
}
