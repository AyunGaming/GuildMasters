<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\IUserDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsDAO;
use division\Data\Database;
use division\Models\Article;
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
			if ($data !== []) {
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

		}
	}

	public function update(Kamenews $kamenews): void {
		try{
			$req = $this->database->prepare('UPDATE kamenews SET titre = ?, description = ? WHERE id = ?');

			$req->bindValue(1, $kamenews->getTitle());
			$req->bindValue(2, $kamenews->getDesc());
			$req->bindValue(3, $kamenews->getId());

			$req->execute();
		} catch (PDOException $e) {

		}
	}

	public function create(Kamenews $kamenews): void {
		try{
			$req = $this->database->prepare('INSERT INTO kamenews (titre, description, date, user) VALUES (?, ?, ?, ?)');

			$req->bindValue(1, $kamenews->getTitle());
			$req->bindValue(2, $kamenews->getDesc());
			$req->bindValue(3, $kamenews->getDate());
			$req->bindValue(4, $kamenews->getWriter()->getId());

			$req->execute();
		} catch (PDOException $e) {

		}
	}

	public function getLastInserted(int $n): array {
		try{
			$req = $this->database->prepare('SELECT * FROM kamenews ORDER BY id DESC LIMIT ?');
			$req->bindParam(1, $n);

			$req->execute();

			$data = $req->fetchAll();

			$kamenewsList = [];
			foreach ($data as $datum) {
				$kamenews = new Kamenews();
				$kamenews->hydrate($datum);
				$kamenewsList[] = $kamenews;
			}

			return $kamenewsList;
		} catch (PDOException $e) {
			throw new \Exception();
		}
	}
}