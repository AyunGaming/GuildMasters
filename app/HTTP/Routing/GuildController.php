<?php

namespace division\HTTP\Routing;

use division\Data\DAO\guild\GuildDAO;
use division\Data\DAO\guild\GuildMemberDAO;
use division\Data\DAO\UserDAO;
use division\Data\Database;
use division\Models\Managers\GuildManager;
use division\Models\Managers\UserManager;
use division\Models\User;
use division\Utils\Flashes;
use division\Utils\FlashMessage;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use const Grpc\STATUS_ABORTED;

class GuildController extends AbstractController{
	private GuildManager $guildManager;
	private UserManager $userManager;

	public function __construct(Database $database){
		parent::__construct($database);
		$this->guildManager = new GuildManager(new GuildDAO($this->database), new GuildMemberDAO($this->database), new UserDAO($this->database));
		$this->userManager = new UserManager(new UserDAO($this->database));
	}

	public function create(Response $response, Request $request): Response{
		$post = $request->getParsedBody();

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		$res = $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('manage-guild'));

		$post['owner'] = $request->getAttribute(User::class);

		try {
			$this->guildManager->create($post);
			$guild = $this->guildManager->getByOwner($post['owner']);
			$this->guildManager->addMember($guild, $post['owner']); // Ajoute l'owner a sa propre guilde (c'est mieux quand même xD)
			Flashes::add(FlashMessage::success("La guilde {$post['name']} a été créé !"));
		} catch (\RuntimeException $e) {
			$res = $response->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)->withHeader('Location', $parser->urlFor('guild-creation'));
			Flashes::add(FlashMessage::danger($e->getMessage()));
		}

		return $res;
	}

	public function deleteGuild(Request $request, Response $response): Response{
		$user = $request->getAttribute(User::class);
		$guild = $this->guildManager->getByOwner($user);

		$this->guildManager->delete($guild);

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('create-guild'));
	}

	public function addGuildMember(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$guild = $this->guildManager->getByName($post['guild']);

		$addUser = $this->userManager->getById($post['user']);
		$this->guildManager->addMember($guild, $addUser);

		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('home'));
	}

	public function acceptMember(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$owner = $this->userManager->getById($post['owner']);
		$guild = $this->guildManager->getByOwner($owner);

		$user = $this->userManager->getById($post['addMember']);
		$this->guildManager->acceptMember($guild, $user);

		Flashes::add(FlashMessage::success("Le membre {$user->getLogin()} a été accepté dans la guilde !"));
		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location' , $parser->urlFor('manage-guild'));
	}

	public function removeMember(Request $request, Response $response): Response {
		$post = $request->getParsedBody();

		$user = $this->userManager->getById($post['addMember']);
		$this->guildManager->removeMember($user);

		Flashes::add(FlashMessage::success("Le membre {$user->getLogin()} a été supprimé de la guilde !"));
		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location' , $parser->urlFor('manage-guild'));
	}

	public function viewManageGuild(Request $request, Response $response, Twig $twig): Response{
		$user = $request->getAttribute(User::class);
		$parser = RouteContext::fromRequest($request)->getRouteParser();
		if(is_null($user)) return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('sign-in'));
		$guild = $this->guildManager->getByOwner($user);

		if($guild == null) return $response->withStatus(StatusCodeInterface::STATUS_FOUND)->withHeader('Location', $parser->urlFor('create-guild'));

		return $twig->render($response, 'guildAdminPanel.twig', [
			'user' => $user,
			'guild' => $guild,
			'flashes' => Flashes::all()
		]);
	}

	/**
	 * @throws SyntaxError
	 * @throws RuntimeError
	 * @throws LoaderError
	 */
	public function viewCreateGuild(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);

		return $twig->render($response, 'guildCreation.twig', [
			'user' => $user,
			'flashes' => Flashes::all()
		]);
	}

	public function viewListGuilds(Request $request, Response $response, Twig $twig): Response {
		$user = $request->getAttribute(User::class);
		$guilds = $this->guildManager->getAll();

		return $twig->render($response, 'guildSearch.twig', [
			'user' => $user,
			'guilds' => $guilds
		]);
	}
}
