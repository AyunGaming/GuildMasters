<?php

namespace division\Data\DAO\character;

use division\Data\DAO\BaseDAO;
use division\Data\DAO\Interfaces\characters\ICharacterTagDAO;
use division\Models\Character;
use division\Models\Tag;
use PDOException;

class CharacterTagDAO extends BaseDAO implements ICharacterTagDAO {

	public function getByCharacter(string $image): array {
		try {
			$req = $this->database->prepare('SELECT * FROM charactertags WHERE idCharacter = ?');

			$req->bindParam(1, $image);
			$req->execute();
			$data = $req->fetchAll();

			if ($data !== false) {
				$tagDAO = new TagDAO($this->database);

				$tags = [];
				foreach ($data as $characterData) {
					$tag = $tagDAO->getById($characterData['idTag']);

					$tags[] = $tag;
				}

				return $tags;
			}
			return [];
		} catch (PDOException) {
			return [];
		}
	}

	public function create(Character $character, Tag $tag): array {
		$req = $this->database->prepare('INSERT INTO charactertags VALUES (?,?)');

		$req->bindValue(1, $character->getImage());
		$req->bindValue(2, $tag->getId());

		try {
			if ($req->execute() !== false) {
				return [$character, $tag];
			}
			return [];
		} catch (PDOException) {
			return [];
		}
	}

	public function delete(Character $character, Tag $tag): array {
		$req = $this->database->prepare('DELETE FROM charactertags WHERE idCharacter = ? AND idTag = ?');

		$req->bindValue(1, $character->getImage());
		$req->bindValue(2, $tag->getId());

		try {
			if ($req->execute() !== false) {
				return [$character, $tag];
			}
			return [];
		} catch (PDOException) {
			return [];
		}
	}

    public function tagsFilterComparison(array $characters, array $filter): array {
        $operator = $filter['operator'] === 'on';
        $filteredCharacters = [];

        foreach ($characters as $character) {
            $tags = $character->getTags();
            $match = false;

            if($operator) { // OR operation
                foreach ($filter['tags'] as $tag) {
                    if(in_array($tag, $tags)) {
                        $match = true;
                        break;
                    }
                }
            } else { // AND operation
                $match = true;
                foreach ($filter['tags'] as $tag) {
                    if (!in_array($tag, $tags)) {
                        $match = false;
                        break;
                    }
                }
            }

            if ($match) {
                $filteredCharacters[] = $character;
            }
        }

        return $filteredCharacters;
    }
}
