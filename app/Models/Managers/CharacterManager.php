<?php

namespace division\Models\Managers;

use division\Data\DAO\Interfaces\ICharacterDAO;

class CharacterManager {
	private ICharacterDAO $characterDAO;

	public function __construct(ICharacterDAO $characterDAO) {
		$this->characterDAO = $characterDAO;
	}

	public function getAllCharacters(): ?array {
		return $this->characterDAO->getAll();
	}
}
