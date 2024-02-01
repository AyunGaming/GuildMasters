<?php

namespace division\Data\DAO\Interfaces\kamenews;

interface IKamenewsArticlesDAO {

	public function getByKamenews(int $kamenewsId): array;

	public function deleteByKamenews(int $kamenewsId): void;
}