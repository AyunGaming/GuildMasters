<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\IUserDAO;
use division\Models\User;
use PDOException;

class UserDAO extends BaseDAO implements IUserDAO {

	public function getById(int $id): ?User {
		try {
			$req = $this->database->prepare('SELECT * FROM users WHERE id = ?');

			$req->bindParam(1,$id);
			$req->execute();
			$data = $req->fetch();


			if($data !== false){
				$user = new User();
				$user->hydrate($data);
				return $user;
			}
			return null;
		} catch (PDOException){
			return null;
		}
	}

	public function getByLogin(string $login): ?User {
		try {
			$req = $this->database->prepare('SELECT * FROM users WHERE login = ?');

			$req->bindParam(1,$login);
			$req->execute();
			$data = $req->fetch();


			if($data !== false){
				$user = new User();
				$user->hydrate($data);
				return $user;
			}
			return null;
		} catch (PDOException $e){
			return null;
		}
	}
}
