<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\kamenews\IArticlesDAO;
use division\Models\Article;
use Exception;
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
			}
			return null;
		} catch (PDOException) {
			return null;
		} finally {
			$req = $this->database->prepare('SELECT image_name FROM articles_images WHERE article_id = ?');
			$req->bindParam(1, $id);

			$req->execute();

			$images = $req->fetchAll();

			if($images !== false){
				$data['image'] = $images;
				$article->hydrate($data);

				return $article;
			}
		}
	}

	public function delete(int $id): void {
		try {
			$req = $this->database->prepare('DELETE FROM articles WHERE id = ?');

			$req->bindParam(1, $id);
			$req->execute();
		} catch (PDOException) {

		}
	}

	public function update(Article $article): void {
		try {
			$req = $this->database->prepare('UPDATE articles SET title = ?, text = ? WHERE id = ?');

			$req->bindValue(1, $article->getTitle());
			$req->bindValue(2, $article->getText());
			$req->bindValue(3, $article->getImage());
			$req->bindValue(4, $article->getId());

			$req->execute();
		} catch (PDOException $e) {

		}
	}

	public function create(Article $article): void {
		try {
			$req = $this->database->prepare('INSERT INTO articles (title, text) VALUES (?, ?)');

			$req->bindValue(1, $article->getTitle());
			$req->bindValue(2, $article->getText());

			$req->execute();
		} catch (PDOException $e) {

		} finally {
			try {
				$id = $this->database->lastInsertId();
				foreach ($article->getImage() as $image) {
					$req = $this->database->prepare('INSERT INTO articles_images (article_id, image_name) VALUES (?, ?)');
					$req->bindValue(1, $id);
					$req->bindValue(2, $image);

					$req->execute();
				}
			} catch (PDOException $e) {
				throw new Exception("Error while inserting images for article" . $article->getTitle() . ":" . $e->getMessage());
			}
		}
	}

	/**
	 * @throws \Exception
	 */
	public function getLastInserted(int $n): array {
		try {
			$req = $this->database->prepare('SELECT * FROM articles ORDER BY id DESC LIMIT ?');
			$req->bindParam(1, $n);

			$req->execute();

			$data = $req->fetchAll();

			$articles = [];
			foreach ($data as $datum) {
				$article = new Article();
				$article->hydrate($datum);
				$articles[] = $article;
			}

			return $articles;
		} catch (PDOException $e) {
			throw new \Exception();
		}
	}
}