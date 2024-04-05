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

	public function delete(int $id): void {
		try{
			$req = $this->database->prepare('DELETE FROM articles WHERE id = ?');

			$req->bindParam(1, $id);
			$req->execute();
		} catch (PDOException) {
			var_dump('error');
			die();
		}
	}

	public function update(Article $article): void {
		try{
			$req = $this->database->prepare('UPDATE articles SET title = ?, text = ?, image = ? WHERE id = ?');

			$req->bindValue(1, $article->getTitle());
			$req->bindValue(2, $article->getText());
			$req->bindValue(3, $article->getImage());
			$req->bindValue(4, $article->getId());

			$req->execute();
		} catch (PDOException $e) {
			var_dump($e->getMessage());
			die();
		}
	}
}