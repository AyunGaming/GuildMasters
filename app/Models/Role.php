<?php

namespace division\Models;

enum Role: string {
	case MEMBER = 'member';

	case MASTER = 'master';

	case ADMIN = 'admin';
}
