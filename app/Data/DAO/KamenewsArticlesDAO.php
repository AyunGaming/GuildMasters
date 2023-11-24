<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\kamenews\IArticlesDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsArticlesDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsDAO;
use division\Data\Database;
use division\Models\Article;
use PDOException;

class KamenewsArticlesDAO extends BaseDAO implements IKamenewsArticlesDAO {
	private IKamenewsDAO $kamenewsDAO;

	private IArticlesDAO $articlesDAO;

	public function __construct(Database $database, IKamenewsDAO $kamenewsDAO, IArticlesDAO $articlesDAO) {
		parent::__construct($database);
		$this->kamenewsDAO = $kamenewsDAO;
		$this->articlesDAO = $articlesDAO;
	}

	public function getByKamenews(int $kamenewsId): array {
		try {
			$req = $this->database->prepare('SELECT * FROM kamenewsarticles WHERE kamenewsId = ?');

			$req->bindParam(1, $kamenewsId);
			$req->execute();

			$data = $req->fetchAll();

			$articles = [];
			if ($data !== null) {
				foreach ($data['articleId'] as $articleData) {
					$article = new Article();
					$article->hydrate($articleData);
					$articles[] = $article;
				}
			}
			return $articles;
		} catch (PDOException) {
			return [];
		}

	}
}