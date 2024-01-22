<?php

namespace division\tests\FakeDAOs;

use division\Data\DAO\IUserDAO;
use division\Models\User;

class FakeUserDAO implements IUserDAO {

	private array $users;

	public function __construct(array $users) {
		$this->users = $users;
	}

	public function getById(int $id): ?User {
		// Simulate fetching a user from the predefined list
		foreach ($this->users as $user) {
			if ($user->getId() === $id) {
				return $user;
			}
		}
		return null;
	}

	public function getByLogin(string $login): ?User {
		// Simulate fetching a user from the predefined list
		foreach ($this->users as $user) {
			if ($user->getLogin() === $login) {
				return $user;
			}
		}
		return null;
	}
}
