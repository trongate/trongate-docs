<?php
define('BASE_URL', 'http://localhost/trongate-docs/');
define('REF_DIR', '0300Comprehensive_Reference_Guide');

if (strpos(BASE_URL, 'localhost') !== false) {
	define('APPPATH', str_replace("\\", "/", dirname(dirname(__FILE__)) . '/').'trongate-docs/');
} else {
	define('APPPATH', str_replace("\\", "/", dirname(dirname(__FILE__)) . '/'));
}

$exclude_dirs = ["css", "fonts", "images", "js", "junk"];
define('EXCLUDE_DIRS', $exclude_dirs);