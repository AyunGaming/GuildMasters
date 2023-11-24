<?php

namespace division\Models\Managers;

use division\Data\DAO\Interfaces\kamenews\IKamenewsDAO;

class KamenewsManager {
	private IKamenewsDAO $kamenewsDAO;

	public function __construct(IKamenewsDAO $kamenewsDAO) {
		$this->kamenewsDAO = $kamenewsDAO;
	}

	public function getAllKamenews(): array {
		return $this->kamenewsDAO->getAll();
	}
}