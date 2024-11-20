<?php

namespace division\Models\Managers;

use DateTime;
use division\Data\DAO\Interfaces\IUserDAO;
use division\Data\DAO\Interfaces\kamenews\IKamenewsDAO;
use division\Models\Kamenews;
use Exception;

class KamenewsManager {
	private IKamenewsDAO $kamenewsDAO;
	private IUserDAO $userDAO;

	public function __construct(IKamenewsDAO $kamenewsDAO, IUserDAO $userDAO) {
		$this->kamenewsDAO = $kamenewsDAO;
		$this->userDAO = $userDAO;
	}

	public function getAllKamenews(): array {
		$kamenews = $this->kamenewsDAO->getAll();
		foreach ($kamenews as $key => $k)
			$kamenews[$key] = $k;

		return $kamenews;
	}

	public function getKamenews(int $id): Kamenews {
		$kamenews = $this->getAllKamenews();
		$res = null;
		foreach ($kamenews as $k) {
			if ($k->getId() === $id) {
				$res = $k;
			}
		}

		return $res;
	}

	/**
	 * @throws Exception
	 */
	public function updateKamenews(array $data): void {
		try{
			$kamenews = new Kamenews();
			$kamenews->hydrate($data);

			$this->kamenewsDAO->update($kamenews);
		} catch (Exception $e) {
			throw new Exception("Kamenews not found: $e->getMessage()");
		}
	}

	public function createKamenews(string $title, string $content): void {
		$kamenews = new Kamenews();
		$today = new DateTime();
		$ayun = $this->userDAO->getByLogin('Ayun');
		$kamenews->hydrate(['titre' => $title, 'writer' => $ayun, 'content' => $content, 'date' => $today->format('Y-m-d'), 'description' => '']);
		$this->kamenewsDAO->create($kamenews);
	}

	public function getLastKamenews(): Kamenews {
		return $this->getKamenews($this->kamenewsDAO->getLastInserted()[0]->getId());
	}
}