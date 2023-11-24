<?php

namespace division\Data\DAO\Interfaces\characters;

use division\Models\Character;

interface ICharacterDAO {

	public function getAll(): array;

	public function getByImage(string $image): ?Character;
}
