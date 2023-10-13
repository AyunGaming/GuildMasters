<?php

namespace division\Utils;

class Flashes {
	public static function add(FlashMessage $flash): void {
		static::checkSession();
		$_SESSION['flashes'][] = [
			'type' => $flash->getType(),
			'message' => $flash->getMessage()
		];
	}

	/**
	 * Returns all the {@link FlashMessage flash messages},
	 * clearing the session data (of flash messages).
	 *
	 * @return array
	 */
	public static function all(): array {
		static::checkSession();
		$flashes = $_SESSION['flashes'];
		// Removes all flash messages from the session
		$_SESSION['flashes'] = [];
		return $flashes;
	}

	private static function checkSession(): void {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		if (!array_key_exists('flashes', $_SESSION)) {
			$_SESSION['flashes'] = [];
		}
	}
}
