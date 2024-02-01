<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\kamenews\IArticlesDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsArticlesDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsDAO;
use division\Data\Database;
use division\Models\Article;
use PDOException;

class KamenewsArticlesDAO extends BaseDAO implements IKamenewsArticlesDAO {

	public function __construct(Database $database) {
		parent::__construct($database);
	}

	public function getByKamenews(int $kamenewsId): array {
		try {
			$req = $this->database->prepare('SELECT * FROM kamenews_articles WHERE kamenewsId = ?');

			$req->bindParam(1, $kamenewsId);
			$req->execute();

			$data = $req->fetchAll();

			$articles = [];
			if ($data !== []) {
				foreach ($data as $articleData){
					$d['id'] = $articleData['articleId'];

					$article = new Article();
					$article->hydrate($d);
					$articles[] = $article;
				}
			}
			return $articles;
		} catch (PDOException) {
			return [];
		}

	}

	public function delete(int $kamenewsId, int $articleId): void {
		try{
			$req = $this->database->prepare('DELETE FROM kamenews_articles WHERE kamenewsId = ? AND articleId = ?');

			$req->bindParam(1, $kamenewsId);
			$req->bindParam(2, $articleId);
			$req->execute();
		} catch (PDOException) {
			var_dump('error');
			die();
		}
	}
}