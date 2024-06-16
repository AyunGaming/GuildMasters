<?php

namespace division\Data\DAO\character;


use division\Data\DAO\BaseDAO;
use division\Data\DAO\Interfaces\characters\ICharacterDAO;
use division\Exceptions\CannotCreateCharacterException;
use division\Exceptions\CannotDeleteCharacterException;
use division\Exceptions\CannotGetCharacterException;
use division\Exceptions\CannotUpdateCharacterException;
use division\Models\Character;
use PDO;
use PDOException;

class CharacterDAO extends BaseDAO implements ICharacterDAO {

	public function create(Character $character): void {
		$statement = $this->database->prepare("INSERT INTO dbl_characters (Image,Rarity,IsLF,Name,Color) VALUES (?,?,?,?,?);");

		$statement->bindValue(1,$character->getImage());
		$statement->bindValue(2,$character->getRarity()->value);
		$statement->bindValue(3,$character->isLF() ? 1 : 0);
		$statement->bindValue(4,$character->getName());
		$statement->bindValue(5,$character->getColor()->value);
		
		try{
			$statement->execute();
		} catch (PDOException $e) {
			throw new CannotCreateCharacterException("Impossible de créer le personnage: " . $character->getImage());
		}
	}

	public function getAll(): array {
		try {
			$statement = $this->database->prepare("SELECT * FROM dbl_characters ORDER BY Image ASC");
            $statement->execute();

			$characters = [];
			$data = $statement->fetchAll();

			foreach ($data as $datum) {
				$character = new Character();
				$character->hydrate($datum);
				$characters[] = $character;
			}

			return $characters;
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
			$req->bindValue(3, $character->isLF() ? 1 : 0);
			$req->bindValue(4, $character->getName());
			$req->bindValue(5, $character->getColor()->value);
			$req->bindValue(6, $oldId);

			if ($req->execute() === false) {
				throw new CannotUpdateCharacterException("Impossible de modifier le personnage");
			}
		} catch (PDOException $PDOException) {
			throw new CannotUpdateCharacterException($PDOException->getMessage());
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

	public function count(): int {
		try{
			$statement = $this->database->prepare("SELECT COUNT(*) FROM dbl_characters");
			$statement->execute();
			return $statement->fetchColumn();
		}
		catch (PDOException $PDOException){
			throw new CannotGetCharacterException($PDOException->getMessage());
		}
	}

    public function searchBy(string $query): array
    {
        try {
            $req = $this->database->prepare($query);
            $req->execute();

            $characters = [];
            $data = $req->fetchAll();

            $characters = [];
            $data = $req->fetchAll();
            foreach ($data as $datum) {
                $character = new Character();
                $character->hydrate($datum);
                $characters[] = $character;
            }

            return $characters;
        }
        catch (PDOException $PDOException){
            throw new CannotGetCharacterException($PDOException->getMessage());
        }
    }


    public function characterSearchQuery(array $data): string {
        // filters = [] => charge tout, sinon charge filtré
        $query = 'SELECT * FROM dbl_characters';
        $operator = $data['operator'] === 'on' ? 'OR' : 'AND';

        $clauses = [];

        if (!empty($data)) {
            if (!empty($data['filtres']['image'])) {
                $clauses[] = "Image LIKE '%{$data['filtres']['image']}%'";
            }
            if (!empty($data['filtres']['name'])) {
                $clauses[] = "Name LIKE '%{$data['filtres']['name']}%'";
            }
            if (!empty($data['filtres']['rarity'])) {
                $clauses[] = "Rarity = '{$data['filtres']['rarity']}'";
            }
            if (!empty($data['filtres']['color'])) {
                $clauses[] = "Color = '{$data['filtres']['color']}'";
            }
            if (!empty($data['filtres']['lf'])) {
                $lf = $data['filtres']['lf'] === 'on' ? 1 : 0;
                $clauses[] = "IsLF = $lf";
            }
        }
        if (!empty($clauses)) {
            $query .= " WHERE " . implode(" $operator ", $clauses);
        }

        $query .= " ORDER BY Image ASC";
        return $query;
    }
}
