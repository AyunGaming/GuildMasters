<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\IUserDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsDAO;
use division\Data\Database;
use division\Models\Kamenews;
use PDOException;

class KamenewsDAO extends BaseDAO implements IKamenewsDAO {
	private IUserDAO $userDAO;

	public function __construct(Database $database,IUserDAO $userDAO) {
		parent::__construct($database);
		$this->userDAO = $userDAO;
	}

	public function getById(int $id): ?Kamenews {
		try {
			$req = $this->database->prepare('SELECT * FROM kamenews WHERE id = ?');

			$req->bindParam(1, $id);
			$req->execute();

			$data = $req->fetch();

			if ($data !== false) {
				$data['writer'] = $this->userDAO->getById($data['user']);
				$kamenews = new Kamenews();
				$kamenews->hydrate($data);

				return $kamenews;
			}
			return null;
		} catch (PDOException) {
			return null;
		}
	}

	public function getAll(): array {
		try {
			$req = $this->database->prepare('SELECT * FROM kamenews');

			$req->execute();

			$data = $req->fetchAll();

			$kamenewsList = [];
			if ($data !== null) {
				foreach ($data as $datum) {
					$datum['writer'] = $this->userDAO->getById($datum['user']);
					$kamenews = new Kamenews();
					$kamenews->hydrate($datum);
					$kamenewsList[] = $kamenews;
				}
			}

			return $kamenewsList;
		} catch (PDOException) {
			return [];
		}
	}

	public function delete(int $id): void {
		try{
			$req = $this->database->prepare('DELETE FROM kamenews WHERE id = ?');

			$req->bindParam(1, $id);
			$req->execute();
		} catch (PDOException) {
			var_dump('error');
			die();
		}
	}
}