<?php

namespace division\Data\DAO\Interfaces\kamenews;

use division\Models\Article;

interface IArticlesDAO {

	public function getById(int $id): ?Article;

	public function update(Article $article): void;

	public function delete(int $id): void;
}