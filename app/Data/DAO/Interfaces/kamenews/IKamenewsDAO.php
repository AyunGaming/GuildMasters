<?php

namespace division\Data\DAO\Interfaces\kamenews;

use division\Models\Kamenews;

interface IKamenewsDAO {

	public function getAll(): array;

	public function getById(int $id): ?Kamenews;

	public function delete(int $id): void;
}