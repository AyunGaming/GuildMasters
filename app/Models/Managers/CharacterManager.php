<?php

namespace division\Models\Managers;


use division\Data\DAO\Interfaces\characters\ICharacterDAO;
use division\Data\DAO\character\CharacterTagDAO;
use division\Data\DAO\Interfaces\characters\ICharacterTagDAO;
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

	public function getPagedCharacters(int $page): ?array {
		$characters = $this->characterDAO->getAll();
		if($page == 1){
			$start = 0;
		}
		else{
			$start = ($page - 1) * 50;
		}
		$displayed = array_slice($characters, $start, 50);
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

		return $displayed;
	}

	public function getCharacterNumber(): int {
		return $this->characterDAO->count();
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

	public function createCharacter(array $data, array $tags): void {
		$character = new Character();
		$character->hydrate($data);

		$this->characterDAO->create($character);

		$character = $this->getByImage($data['Id']);
		foreach ($tags as $tag) {
			$this->characterTagDAO->create($character, $tag);
		}
	}

    public function searchBy(string $search_param, string $search_input): ?array {

    }
}
