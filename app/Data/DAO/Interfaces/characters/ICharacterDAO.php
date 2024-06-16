<?php

namespace division\Data\DAO\Interfaces\characters;

use division\Models\Character;

interface ICharacterDAO {

	public function create(Character $character): void;

	public function getAll(): array;

	public function getByImage(string $image): Character;

	public function update(Character $character, string $oldId): void;

	public function delete(Character $character): void;

	public function count(): int;

    public function searchBy(string $query): array;
}
