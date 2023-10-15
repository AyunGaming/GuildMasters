<?php

namespace division\Data\DAO;

use division\Models\Tag;

interface ITagDAO {
	public function getById(int $id): ?Tag;
}
