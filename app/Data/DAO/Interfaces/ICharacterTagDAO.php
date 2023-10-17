<?php

namespace division\Data\DAO\Interfaces;

use division\Models\Character;

interface ICharacterTagDAO {
	public function getByCharacter(string $image): ?Character;
}
