<?php

namespace division\Models\Managers;

use division\Data\DAO\Interfaces\IUserDAO;
use division\Models\User;

class UserManager {
	private IUserDAO $userDAO;

	public function __construct(IUserDAO $userDAO){
		$this->userDAO = $userDAO;
	}

	public function checkLogin(string $login, string $mdp): ?User {
		$user = $this->userDAO->getByLogin($login);
		
		if($user !== null && $this->checkPassword($mdp,$user)){
			return $user;
		}
		return null;
	}

	public function checkPassword(string $mdp, User $user): bool{

		return password_verify($mdp, $user->getPassword());
	}

	public function getById(int $id): ?User {
		return $this->userDAO->getById($id);
	}

    public function register(string $login, string $password)
    {

        $pwd = password_hash($password, PASSWORD_BCRYPT);
        $this->userDAO->register($login, $pwd);

        return $this->checkLogin($login, $password);
    }
}
