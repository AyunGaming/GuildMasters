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


	public static function primary(string $message): static {
		return new static('primary', $message);
	}


	public static function secondary(string $message): static {
		return new static('secondary', $message);
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


	public static function light(string $message): static {
		return new static('light', $message);
	}


	public static function dark(string $message): static {
		return new static('dark', $message);
	}
}
