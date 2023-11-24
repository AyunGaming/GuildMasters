<?php

namespace division\Data\DAO;

use division\Data\DAO\Interfaces\kamenews\IKamenewsDAO;
use division\Models\Kamenews;
use PDOException;

class KamenewsDAO extends BaseDAO implements IKamenewsDAO {

	public function getById(int $id): ?Kamenews {
		try {
			$req = $this->database->prepare('SELECT * FROM kamenews WHERE id = ?');

			$req->bindParam(1, $id);
			$req->execute();

			$data = $req->fetch();

			if ($data !== false) {
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
}