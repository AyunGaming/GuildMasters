<?php

namespace division\Data\DAO\Interfaces\characters;

use division\Models\Tag;

interface ITagDAO {
	public function getById(int $id): ?Tag;
}
