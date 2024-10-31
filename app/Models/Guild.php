<?php

namespace division\Models;


class Guild {
	private string $name;

	private string $description;

	private User $owner;

	public function hydrate(array $data): void {
		if(array_key_exists('name', $data)) {
			$this->name = $data['name'];
		}
		if(array_key_exists('description', $data)) {
			$this->description = $data['description'];
		}
		if(array_key_exists('owner', $data)) {
			$this->owner = $data['owner'];
		}
	}

	public function getName(): string {
		return $this->name;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function getOwner(): User {
		return $this->owner;
	}
}
