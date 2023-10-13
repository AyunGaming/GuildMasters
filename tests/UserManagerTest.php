<?php

namespace division\tests;

use division\Models\Managers\UserManager;
use division\Models\User;
use division\tests\FakeDAOs\FakeUserDAO;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase {
	public function testCheckLogins() {

		$user1 = new User();
		$user2 = new User();

		$user1->hydrate(['login' => 'bob','password' => 'secret']);
		$user2->hydrate(['login' => 'alice','password' => password_hash('MeileurSecret', PASSWORD_BCRYPT)]);

		$fakeUserDAO = new FakeUserDAO([$user1, $user2]);
		$userManager = new UserManager($fakeUserDAO);

		$loggedInUser = $userManager->checkLogin('alice', 'MeileurSecret');
		$this->assertInstanceOf(User::class, $loggedInUser);

		$loggedInUser = $userManager->checkLogin('bob', 'ez');
		$this->assertNull($loggedInUser);

		$loggedInUser = $userManager->checkLogin('Eva', 'secret2');
		$this->assertNull($loggedInUser);
	}
}
