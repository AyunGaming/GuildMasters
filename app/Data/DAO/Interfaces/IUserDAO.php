<?php

namespace division\Data\DAO\Interfaces;

use division\Models\User;

interface IUserDAO {
	public function getById(int $id): ?User;

	public function getByLogin(string $login): ?User;
}
