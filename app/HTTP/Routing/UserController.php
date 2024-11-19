<?php

namespace division\HTTP\Routing;

use division\Data\DAO\UserDAO;
use division\Data\Database;
use division\Models\Managers\UserManager;
use division\Models\User;
use division\Utils\Alerts;
use division\Utils\alerts\Alert;
use division\Utils\alerts\AlertTypes;
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
					Alerts::add(new Alert("Le format du mot de passe est incorrect", AlertTypes::ERROR,
						[
							'8 caractères minimum',
							'Minimum 1 majuscules',
							'Minimum une minuscule',
							'Minimum un nombre',
							'Minimum un caractère spécial'
						]));
					return $response->withStatus(StatusCodeInterface::STATUS_FOUND)
						->withHeader("Location", $parser->urlFor('sign-in'));
				}

				$manager = new UserManager(new UserDAO($this->database));
				$user = $manager->register($login, $password);

				if ($user !== null) {
					$_SESSION['user_id'] = $user->getId();
					Alerts::add(new Alert("Votre compte a bien été créé", AlertTypes::SUCCESS));
					return $response->withStatus(StatusCodeInterface::STATUS_FOUND)
						->withHeader('Location', $parser->urlFor('home'));
				}
			}

			Alerts::add(new Alert('Mot de passe différent de la confirmation !', AlertTypes::ERROR));
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
				Alerts::add(new Alert("Bienvenue ".$user->getLogin()." !", AlertTypes::SUCCESS));
				return $response->withStatus(StatusCodeInterface::STATUS_FOUND)
					->withHeader('Location', $parser->urlFor('home'));
			}

			// Invalid login or credentials, alert the user
			Alerts::add(new Alert('Login ou mot de passe incorrects !',AlertTypes::ERROR));
			return $response->withStatus(StatusCodeInterface::STATUS_FOUND)
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
			'alerts' => Alerts::all(),
			'user' => $user
		]);
	}
}
