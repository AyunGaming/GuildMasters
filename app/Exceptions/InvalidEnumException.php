<?php

namespace division\Exceptions;

use mysql_xdevapi\Statement;
use RuntimeException;

class InvalidEnumException extends RuntimeException {
	public function __construct(private readonly string $enumClass, private readonly string|int $invalidValue) {
		parent::__construct("Value '$this->invalidValue' is not a member of enumeration '$this->enumClass'");
	}

	public function getEnumClass(): string{
		return $this->enumClass;
	}

	public function getInvalidValue(): string|int{
		return $this->invalidValue;
	}
}
