<?php

namespace division\Models\Managers;

use division\Data\DAO\CharacterTagDAO;
use division\Data\DAO\Interfaces\ICharacterDAO;
use division\Data\DAO\Interfaces\ICharacterTagDAO;
use division\Exceptions\CannotUpdateCharacterException;
use division\Models\Character;
use Exception;

class CharacterManager {
	private ICharacterDAO $characterDAO;
	private ICharacterTagDAO $characterTagDAO;

	public function __construct(ICharacterDAO $characterDAO, ICharacterTagDAO $characterTagDAO) {
		$this->characterDAO = $characterDAO;
		$this->characterTagDAO = $characterTagDAO;
	}

	public function getAllCharacters(): ?array {
		$characters = $this->characterDAO->getAll();

		foreach ($characters as $character) {
			$data['Tags'] = [];
			$characterTags = $this->characterTagDAO->getByCharacter($character->getImage());
			$i = 0;
			foreach ($characterTags as $tag) {
				$data['Tags'][$i]['id'] = $tag->getId();
				$data['Tags'][$i]['name'] = $tag->getName();
				$i++;
			}
			$character->hydrate($data);

		}

		return $characters;
	}

	public function getByImage(string $image): Character {
		return $this->characterDAO->getByImage($image);
	}

	public function updateCharacter(array $data): void {
		try{
			$character = new Character();
			$character->hydrate($data);

			$this->characterDAO->update($character,$data['oldId']);
		}
		catch (Exception $e){
			throw new CannotUpdateCharacterException($e->getMessage());
		}
	}

	public function createCharacter(array $data): void {
		$character = new Character();
		$character->hydrate($data);

		$this->characterDAO->create($character);
	}
}
