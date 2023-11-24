<?php

namespace division\Data\DAO\character;

use division\Data\DAO\BaseDAO;
use division\Data\DAO\Interfaces\characters\ICharacterDAO;
use division\Models\Character;
use PDOException;

class CharacterDAO extends BaseDAO implements ICharacterDAO {

	public function getAll(): array {
		try {
			$statement = $this->database->prepare('SELECT * FROM dbl_characters');

			$statement->execute();

			$data = $statement->fetchAll();
			$characters = [];
			foreach ($data as $datum) {
				$character = new Character();
				$character->hydrate($datum);
				$characters[] = $character;
			}
			return $characters;
		} catch (PDOException) {
			return [];
		}
	}


	public function getByImage(string $image): ?Character {
		try {
			$req = $this->database->prepare('SELECT * FROM dbl_characters WHERE Image = ?');

			$req->bindParam(1, $image);
			$req->execute();
			$data = $req->fetch();

			if ($data !== false) {
				$character = new Character();
				$character->hydrate($data);
				return $character;
			}
			return null;
		} catch (PDOException) {
			return null;
		}
	}
}
