<?php

namespace division\Data\DAO\Interfaces\characters;

use division\Models\Character;
use division\Models\Tag;

interface ICharacterTagDAO {
	public function getByCharacter(string $image): array;

	public function create(Character $character, Tag $tag): array;

	public function delete(Character $character, Tag $tag): array;

    public function tagsFilterComparison(array $characters, array $filter): array;
}
