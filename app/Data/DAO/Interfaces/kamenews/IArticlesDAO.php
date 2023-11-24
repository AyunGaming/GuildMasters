<?php

namespace division\Data\DAO\Interfaces\kamenews;

use division\Models\Article;

interface IArticlesDAO {

	public function getById(int $id): ?Article;
}