<?php

namespace division\Data\DAO\Interfaces\characters;

use division\Models\Character;

interface ICharacterTagDAO {
	public function getByCharacter(string $image): ?Character;
}
