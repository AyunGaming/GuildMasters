<?php

namespace division\Exceptions;

class CannotParseFileException extends \RuntimeException {
	public function __construct(string $message) {
		parent::__construct($message);
	}
}
