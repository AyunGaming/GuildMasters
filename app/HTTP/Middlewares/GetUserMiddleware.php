<?php

namespace division\HTTP\Middlewares;

use division\Models\Managers\UserManager;
use division\Models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetUserMiddleware implements MiddlewareInterface {
	public function __construct(private UserManager $users){
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
		if(array_key_exists('user_id',$_SESSION)){
			$userId = $_SESSION['user_id'];
			$user = $this->users->getById($userId);
			if($user !== null){
				$request = $request->withAttribute(User::class, $user);
			}
		}
		return $handler->handle($request);
	}
}
