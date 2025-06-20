<?php

namespace division\Utils;

use division\Utils\alerts\Alert;

class Alerts {
	public static function add(Alert $alert): void {
		static::checkSession();
		$_SESSION['alerts'][] = [
			'type' => $alert->getType(),
			'infos' => $alert->getInfos(),
			'message' => $alert->getMessage()
		];
	}

	/**
	 * Returns all the {@link Alert alert},
	 * clearing the session data (of alerts).
	 *
	 * @return array
	 */
	public static function all(): array {
		static::checkSession();
		$alerts = $_SESSION['alerts'];
		// Removes all flash messages from the session
		$_SESSION['alerts'] = [];
		return $alerts;
	}

	private static function checkSession(): void {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		if (!array_key_exists('alerts', $_SESSION)) {
			$_SESSION['alerts'] = [];
		}
	}
}
