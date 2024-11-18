<?php

namespace division\HTTP;

use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\ErrorRendererInterface;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use Throwable;

final class HtmlErrorRenderer implements ErrorRendererInterface {

	private Twig $twig;
	private Response $response;

	public function __construct(Twig $twig, Response $response)
	{
		$this->twig = $twig;
		$this->response = $response;
	}


	public function __invoke(Throwable $exception, bool $displayErrorDetails): string {
		$title = 'Error';
		$message = 'An error occurred';
		$code = $exception->getCode();

		if($exception instanceof HttpNotFoundException)
		{
			$title = 'Page not found';
			$message = 'The page you are looking for could not be found';
		}

		return $this->renderPage($title, $message, $code);
	}

	public function renderPage(string $title, string $message, int $code): string {

		return (string)$this->twig->render($this->response, 'error.twig', [
			'title' => $title,
			'message' => $message,
			'code' => $code,
		])->getBody();
	}
}
