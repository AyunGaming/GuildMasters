<?php

namespace division\Configs;

use division\Exceptions\CannotParseFileException;

readonly class DatabaseConfig {
	private function __construct(private array $data) {
	}

	public function getHostName(): string {
		if(!array_key_exists('server', $this->data)){
			return 'localhost';
		}
		return array_key_exists('host',$this->data['server']) ? $this->data['server']['host']: 'localhost';
	}

	public function getPort(): int{
		if(!array_key_exists('server', $this->data)){
			return 3306;
		}
		if(array_key_exists('port',$this->data['server'])){
			$port = (int)$this->data['server']['port'];
			return ($port > 0 && $port <= 65535) ? $port : 3306;
		}
		return 3306;
	}

	public function getDatabaseName(): string{
		if(!array_key_exists('server', $this->data)){
			return '';
		}
		return array_key_exists('dbname',$this->data['server']) ? $this->data['server']['dbname'] : '';
	}

	public function getCharset(): string{
		if(!array_key_exists('server',$this->data)){
			return 'utf8mb4';
		}
		return array_key_exists('charset',$this->data['server']) ? $this->data['server']['charset'] : 'utf8mb4';
	}

	public function getUserLogin(): string{
		if(!array_key_exists('server',$this->data)){
			return 'root';
		}
		return array_key_exists('login',$this->data['server']) ? $this->data['server']['login'] : 'root';
	}

	public function getUserPassword(): string{
		if(!array_key_exists('server',$this->data)){
			return '';
		}
		return array_key_exists('passwd',$this->data['server']) ? $this->data['server']['passwd'] : '';
	}

	public static function load(): static{
		$data = parse_ini_file(__DIR__.'/../../config/database.ini');
		if($data === false){
			throw new CannotParseFileException("The config file `database.ini` cannot be parsed");
		}
		return new static($data);
	}
}
