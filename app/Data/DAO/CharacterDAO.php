<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\ICharacterDAO;
use division\Models\Character;
use PDOException;

class CharacterDAO extends BaseDAO implements ICharacterDAO {

	public function getAll(): array {
		try {
			$statement = $this->database->prepare('SELECT * FROM dbl_characters');

			if($statement->execute() === false){
				return [];
			}

			$characters = [];
			foreach (($statement->fetchAll() ?? []) as $datum) {
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


	public function update(Character $character, string $oldId): ?Character {
		try {
			$req = $this->database->prepare('UPDATE dbl_characters SET Image = ?, Rarity = ?, IsLF = ?, Name = ?, Color = ? WHERE Image = ?');

			$req->bindValue(1, $character->getImage());
			$req->bindValue(2, $character->getRarity()->value);
			$req->bindValue(3, $character->isLF());
			$req->bindValue(4, $character->getName());
			$req->bindValue(5, $character->getColor()->value);
			$req->bindValue(6, $oldId);

			if ($req->execute() !== false) {
					return $character;
			}
			return null;
		} catch (PDOException) {
			return null;
		}
	}
}
