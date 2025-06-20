<?php

namespace division\Data\DAO;

use division\Data\Database;

class BaseDAO {
	protected ?Database $database;

	public function __construct(?Database $database){
		$this->database = $database;
	}
}
