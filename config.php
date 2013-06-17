<?php

	# Constants, by environment
	define('DOMAIN', 'dev.local');
	define('BASE', '/var/www/'.DOMAIN.'/htdocs');

	# Class Autoloader
	function class_autoloader($class_name) {
		global $version;
		$path = './classes/' . $version . '/' . $class_name . '.php';

		if(file_exists($path)) {
			require_once $path;
		}
		else {
			header("HTTP/1.0 404 API Object Not Found");die();
		}
	}
	spl_autoload_register('class_autoloader', true);

?>
