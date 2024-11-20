<?php

namespace division\Utils\alerts;

enum AlertTypes: string {
	case SUCCESS = 'success';
	case INFO = 'info';
	case WARNING = 'warning';
	case ERROR = 'error';
}
