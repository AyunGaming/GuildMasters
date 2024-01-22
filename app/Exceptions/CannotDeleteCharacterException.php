<?php

namespace division\Exceptions;

use RuntimeException;

class CannotDeleteCharacterException extends RuntimeException {
	public function __construct(string $message) {
		parent::__construct($message);
	}

}
