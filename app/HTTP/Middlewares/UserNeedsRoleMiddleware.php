<?php

namespace division\HTTP\Middlewares;

use division\Models\Enums\Role;
use division\Utils\Flashes;
use division\Utils\FlashMessage;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class UserNeedsRoleMiddleware implements MiddlewareInterface {
	public function __construct(Private Role $role){
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
		$user = $request->getAttribute(User::class);
		if($user === null){
			Flashes::add(FlashMessage::danger('Vous devez être connecté pour accéder à cette page !'));
			return $this->redirect($request, 'sign-in');
		}
		if($user->getRole() !== $this->role){
			Flashes::add(FlashMessage::danger('Vous n\'êtes pas autorisé à accéder à cette page !'));
			return $this->redirect($request, 'home');
		}
		return $handler->handle($request);
	}

	private function redirect(ServerRequestInterface $request, string $routeName): ResponseInterface {
		$parser = RouteContext::fromRequest($request)->getRouteParser();
		return (new Response(StatusCodeInterface::STATUS_FOUND))
			->withHeader('Location', $parser->urlFor($routeName));
	}
}
