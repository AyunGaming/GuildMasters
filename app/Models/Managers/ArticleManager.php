<?php

namespace division\Models\Managers;

use division\Data\DAO\Interfaces\kamenews\IArticlesDAO;

class ArticleManager {
	private IArticlesDAO $articlesDAO;

	public function __construct(IArticlesDAO $articlesDAO){
		$this->articlesDAO = $articlesDAO;
	}
}