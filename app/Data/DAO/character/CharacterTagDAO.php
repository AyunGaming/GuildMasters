<?php

namespace division\Data\DAO\character;

use division\Data\DAO\BaseDAO;
use division\Data\DAO\Interfaces\characters\ICharacterTagDAO;
use division\Models\Character;
use division\Models\Tag;
use PDOException;

class CharacterTagDAO extends BaseDAO implements ICharacterTagDAO {

	public function getByCharacter(string $image): ?Character {
		try{
			$req = $this->database->prepare('SELECT * FROM charactertags WHERE idCharacter = ?');

			$req->bindParam(1,$image);
			$req->execute();
			$data = $req->fetch();


			if($data !== false){
				$characterDAO = new CharacterDAO($this->database);
				$character = $characterDAO->getByImage($data['idCharacter']);
				$tags = [];
				if($character !== null){
					foreach ($data['idTag'] as $tagData){
						$tag = new Tag();
						$tag->hydrate($tagData);
						$tags[] = $tag;
					}
					$tagData['tags'] = $tags;
					$character->hydrate($tagData);
					return $character;
				}
				return null;
			}
			return null;
		} catch (PDOException){
			return null;
		}
	}
}
