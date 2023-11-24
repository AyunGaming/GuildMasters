<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\kamenews\IArticlesDAO;
use division\Models\Article;
use PDOException;

class ArticlesDAO extends BaseDAO implements IArticlesDAO {

	public function getById(int $id): ?Article {
		try {
			$req = $this->database->prepare('SELECT * FROM articles WHERE id = ?');

			$req->bindParam(1, $id);
			$req->execute();

			$data = $req->fetch();

			if ($data !== false) {
				$article = new Article();
				$article->hydrate($data);

				return $article;
			}
			return null;
		} catch (PDOException) {
			return null;
		}
	}
}