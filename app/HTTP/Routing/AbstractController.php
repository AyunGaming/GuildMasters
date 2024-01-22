<?php

namespace division\HTTP\Routing;

use division\Data\Database;

abstract class AbstractController {
	public function __construct(protected readonly Database $database) {
	}
}
