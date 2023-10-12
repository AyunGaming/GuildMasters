<?php

namespace tests;

use division\Configs\DatabaseConfig;
use division\Data\Database;
use division\Exceptions\CannotParseFileException;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase{
	protected function setUp(): void {
		$this->assertFileExists(
			__DIR__.'/../config/database.ini', 'No db file found'
		);
	}

	public function testLoadConfig(): void{
		$this->expectNotToPerformAssertions();
		try {
			DatabaseConfig::load();
		} catch (CannotParseFileException $exception){
			$this->fail($exception->getMessage());
		}
	}

	public function testDatabaseConnect():void{
		$this->expectNotToPerformAssertions();
		try {
			new Database(DatabaseConfig::load());
		} catch (\PDOException $pdo){
			$this->fail($pdo->getMessage());
		}
	}
}
