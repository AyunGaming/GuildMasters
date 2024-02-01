<?php

namespace division\Data\DAO\Interfaces\kamenews;

interface IKamenewsArticlesDAO {

	public function getByKamenews(int $kamenewsId): array;

	public function delete(int $kamenewsId, int $articleId): void;
}