<?php

namespace division\Utils\alerts;

class Alert {

	private string $message;

	private array $infos;

	private string $type;


	public function __construct(string $message, AlertTypes $type, array $infos = []) {
		$this->message = $message;
		$this->type = $type->value;
		$this->infos = $infos;
	}

	public function getMessage(): string {
		return $this->message;
	}

	public function getInfos(): array {
		return $this->infos;
	}

	public function getType(): string {
		return $this->type;
	}
}