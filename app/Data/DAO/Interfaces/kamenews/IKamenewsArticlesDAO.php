<?php

namespace division\Data\DAO\Interfaces\kamenews;

interface IKamenewsArticlesDAO {

	public function getByKamenews(int $kamenewsId): array;
}