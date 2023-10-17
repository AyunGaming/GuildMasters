<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\ITagDAO;
use division\Models\Tag;
use PDOException;

class TagDAO extends BaseDAO implements ITagDAO {

	public function getById(int $id): ?Tag {
		try {
			$req = $this->database->prepare('SELECT * FROM tags WHERE id = ?');

			$req->bindParam(1, $id);
			$req->execute();
			$data = $req->fetch();


			if ($data !== false) {
				$tag = new Tag();
				$tag->hydrate($data);
				return $tag;
			}
			return null;
		} catch (PDOException) {
			return null;
		}
	}
}
