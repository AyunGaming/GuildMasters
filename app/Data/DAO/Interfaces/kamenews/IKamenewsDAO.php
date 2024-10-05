<?php

namespace division\Data\DAO\Interfaces\kamenews;

use division\Models\Kamenews;

interface IKamenewsDAO {

	public function getAll(): array;

	public function getById(int $id): ?Kamenews;

	public function delete(int $id): void;

	public function update(Kamenews $kamenews): void;

	public function create(Kamenews $kamenews): void;

	/**
	 * @param int $n
	 * @return array<int, Kamenews>
	 */
	public function getLastInserted(int $n = 1): array;
}