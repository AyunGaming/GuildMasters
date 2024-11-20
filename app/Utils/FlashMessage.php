<?php

namespace division\Utils;

readonly class FlashMessage {
	private function __construct(private string $type, private string $message) {
	}

	public function getType(): string {
		return $this->type;
	}

	public function getMessage(): string {
		return $this->message;
	}



	public static function success(string $message): static {
		return new static('success', $message);
	}


	public static function danger(string $message): static {
		return new static('danger', $message);
	}


	public static function warning(string $message): static {
		return new static('warning', $message);
	}


	public static function info(string $message): static {
		return new static('info', $message);
	}
}
