<?php

namespace division\Data\DAO\Interfaces\kamenews;

interface IKamenewsArticlesDAO {

	public function getByKamenews(int $kamenewsId): array;

	public function deleteByKamenews(int $kamenewsId): void;

	public function deleteByArticle(int $articleId): void;

	public function create(int $kamenewsId, int $articleId, int $position): void;
}