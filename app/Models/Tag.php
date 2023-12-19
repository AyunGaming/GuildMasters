<?php

namespace division\Models;

use JsonSerializable;

class Tag implements JsonSerializable {
	private int $id;
	private string $name;

	public function getId(): int {
		return $this->id;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): void {
		$this->name = $name;
	}

	public function hydrate(array $data): void {
		if(array_key_exists('id',$data)){
			$this->id = $data['id'];
		}

		if(array_key_exists('name',$data)){
			$this->name = $data['name'];
		}
	}

	public function __toString(): string {
		return $this->name;
	}


	public function jsonSerialize(): mixed {
		return ['id' => $this->id, 'name' => $this->name];
	}
}
