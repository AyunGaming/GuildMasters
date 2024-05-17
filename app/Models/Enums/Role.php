<?php

namespace division\Models\Enums;

enum Role: string {
	case MEMBER = 'member';

	case KAMENEWS = 'kamenews';

	case MASTER = 'master';

	case ADMIN = 'admin';
}
