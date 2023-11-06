<?php

namespace division\Data\DAO\Interfaces;

use division\Models\Character;

interface ICharacterDAO {

	public function getAll(): array;

	public function getByImage(string $image): ?Character;

	public function update(Character $character, string $oldId): ?Character;
}
