<?php

namespace division\Exceptions;

class CannotCreateCharacterException extends \RuntimeException {
	public function __construct(string $message) {
		parent::__construct($message);
	}
}
