<?php

namespace division\Exceptions;

use RuntimeException;

class CannotGetCharacterException extends RuntimeException {
	public function __construct(string $message) {
		parent::__construct($message);
	}
}
