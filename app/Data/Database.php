<?php

namespace division\Data;

use division\Configs\DatabaseConfig;
use PDO;

class Database extends \PDO {

	public function __construct(DatabaseConfig $config) {
		$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $config->getHostName(), $config->getPort(), $config->getDatabaseName(),
			 $config->getCharset()
		);
		parent::__construct($dsn, $config->getUserLogin(), $config->getUserPassword());
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		$this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
	}
}
