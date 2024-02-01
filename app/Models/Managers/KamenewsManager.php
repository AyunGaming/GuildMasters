<?php

namespace division\Models\Managers;

use division\Data\DAO\Interfaces\IUserDAO;
use division\Data\DAO\Interfaces\kamenews\IArticlesDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsArticlesDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsDAO;
use division\Models\Kamenews;

class KamenewsManager {
	private IKamenewsDAO $kamenewsDAO;
	private IKamenewsArticlesDAO $kamenewsArticlesDAO;
	private IArticlesDAO $articlesDAO;

	public function __construct(IKamenewsDAO $kamenewsDAO, IArticlesDAO $articlesDAO, IKamenewsArticlesDAO $kamenewsArticlesDAO) {
		$this->kamenewsDAO = $kamenewsDAO;
		$this->articlesDAO = $articlesDAO;
		$this->kamenewsArticlesDAO = $kamenewsArticlesDAO;
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
}