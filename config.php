<?php
define('BASE_URL', 'http://localhost/trongate-docs/');
define('APPPATH', str_replace("\\", "/", dirname(__FILE__) . '/'));

// The name of the dir that stores the comprehensive reference guide.
define('REF_DIR', '0300Comprehensive_Reference_Guide');

// An array of directories that will not be added to the website navigation.
$exclude_dirs = ["css", "fonts", "images", "js", "junk"];
define('EXCLUDE_DIRS', $exclude_dirs);