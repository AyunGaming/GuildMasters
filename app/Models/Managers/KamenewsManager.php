<?php

namespace division\Models\Managers;

use division\Data\DAO\Interfaces\IUserDAO;
use division\Data\DAO\Interfaces\kamenews\IArticlesDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsArticlesDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsDAO;
use division\Models\Article;
use division\Models\Kamenews;
use division\Utils\Cookie;
use Exception;

class KamenewsManager {
	private IKamenewsDAO $kamenewsDAO;
	private IKamenewsArticlesDAO $kamenewsArticlesDAO;
	private IArticlesDAO $articlesDAO;
	private IUserDAO $userDAO;

	public function __construct(IKamenewsDAO $kamenewsDAO, IArticlesDAO $articlesDAO, IKamenewsArticlesDAO $kamenewsArticlesDAO, IUserDAO $userDAO) {
		$this->kamenewsDAO = $kamenewsDAO;
		$this->articlesDAO = $articlesDAO;
		$this->kamenewsArticlesDAO = $kamenewsArticlesDAO;
		$this->userDAO = $userDAO;
	}

	public function getAllKamenews(): array {
		$kamenews = $this->kamenewsDAO->getAll();
		foreach ($kamenews as $key => $k) {
			$kameArticles = $this->kamenewsArticlesDAO->getByKamenews($k->getId());
			if($kameArticles !== []){
				foreach ($kameArticles as $ka) {
					$article = $this->articlesDAO->getById($ka->getId());
					$k->addArticle($article);
				}
			}
			$kamenews[$key] = $k;
		}

		return $kamenews;
	}

	public function getKamenews(int $id): Kamenews {
		$kamenews = $this->getAllKamenews();
		$res = null;
		foreach ($kamenews as $k) {
			if ($k->getId() === $id) {
				$res = $k;
			}
		}

		return $res;
	}

	public function deleteKamenews(int $id): void {
		$articles = $this->kamenewsArticlesDAO->getByKamenews($id);
		$this->kamenewsArticlesDAO->deleteByKamenews($id);
		foreach ($articles as $a) {
			$this->articlesDAO->delete($a->getId());
		}
		$this->kamenewsDAO->delete($id);
	}

	/**
	 * @throws Exception
	 */
	public function updateArticle(array $data): void {
		try{
			$article = new Article();
			$article->hydrate($data);

			$this->articlesDAO->update($article);
		} catch (Exception $e) {
			throw new Exception("Article not found: $e->getMessage()");
		}
	}

	/**
	 * @throws Exception
	 */
	public function updateKamenews(array $data): void {
		try{
			$kamenews = new Kamenews();
			$kamenews->hydrate($data);

			$this->kamenewsDAO->update($kamenews);
		} catch (Exception $e) {
			throw new Exception("Kamenews not found: $e->getMessage()");
		}
	}

	public function addKamenews(array $data): void {
		$data['writer'] = $this->userDAO->getByLogin($data['writer']);
		$kamenews = new Kamenews();
		$kamenews->hydrate($data);
		$this->kamenewsDAO->create($kamenews);

		$kamenews = $this->kamenewsDAO->getLastInserted(1);

		$articles = $this->articlesDAO->getLastInserted($data['nbArticles']);
		$pos = 0;
		foreach ($articles as $article) {
			$this->addArticleToKamenews($kamenews[0]->getId(), $article->getId(), $pos++);
		}
	}

	public function addArticle(array $data): void {
		$article = new Article();
		$article->hydrate($data);

		$this->articlesDAO->create($article);
	}

	public function addArticleToKamenews(int $kamenewsId, int $articleId, int $position): void {
		$this->kamenewsArticlesDAO->create($kamenewsId, $articleId, $position);
	}

	public function deleteArticle(int $id): void {
		$this->kamenewsArticlesDAO->deleteByArticle($id);
		$this->articlesDAO->delete($id);
	}

	public function getLastKamenews(): Kamenews {
		return $this->getKamenews($this->kamenewsDAO->getLastInserted()[0]->getId());
	}
}