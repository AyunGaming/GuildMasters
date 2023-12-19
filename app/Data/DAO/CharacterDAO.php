<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\ICharacterDAO;
use division\Exceptions\CannotDeleteCharacterException;
use division\Exceptions\CannotGetCharacterException;
use division\Exceptions\CannotUpdateCharacterException;
use division\Models\Character;
use JsonSerializable;
use PDOException;

class CharacterDAO extends BaseDAO implements ICharacterDAO {

	public function getAll(): array {
		try {
			$statement = $this->database->prepare('SELECT * FROM dbl_characters');

			if($statement->execute() !== false){
				$characters = [];
				foreach (($statement->fetchAll() ?? []) as $datum) {
					$character = new Character();
					$character->hydrate($datum);
					$characters[] = $character;
				}
				return $characters;
			}
			throw new CannotGetCharacterException("Impossible de récupérer les personnages");
		} catch (PDOException $PDOException) {
			throw new CannotGetCharacterException($PDOException->getMessage());
		}
	}


	public function getByImage(string $image): Character {
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

			throw new CannotGetCharacterException("Impossible de récupérer le personnage");
		} catch (PDOException $PDOException) {
			throw new CannotUpdateCharacterException($PDOException->getMessage());
		}
	}


	public function update(Character $character, string $oldId): void {
		try {
			$req = $this->database->prepare('UPDATE dbl_characters SET Image = ?, Rarity = ?, IsLF = ?, Name = ?, Color = ? WHERE Image = ?');

			$req->bindValue(1, $character->getImage());
			$req->bindValue(2, $character->getRarity()->value);
			$req->bindValue(3, $character->isLF());
			$req->bindValue(4, $character->getName());
			$req->bindValue(5, $character->getColor()->value);
			$req->bindValue(6, $oldId);

			if ($req->execute() === false) {
				throw new CannotUpdateCharacterException("Impossible de modifier le personnage");
			}
		} catch (PDOException $PDOException) {
			//throw new CannotUpdateCharacterException($PDOException->getMessage());
			var_dump($code = $PDOException->errorInfo[1]);
			die();
		}
	}

	public function delete(Character $character): void {
		try {
			$req = $this->database->prepare('DELETE FROM dbl_characters WHERE Image = ?');

			$req->bindValue(1, $character->getImage());

			if ($req->execute() === false) {
				throw new CannotDeleteCharacterException("Impossible de supprimer le personnage !");
			}
		} catch (PDOException $PDOException) {
			throw new CannotDeleteCharacterException($PDOException->getMessage());
		}
	}
}
