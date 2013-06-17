<?php 
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	#echo $_SERVER['REQUEST_URI'];
	$request_url = explode('/', $_SERVER['REQUEST_URI']);

	#print_r($request_url);

#	file structure:
#	./classes/[version]/[class-name].php <- loaded via php autoloader
#	./classes/[version]/external/[class-name].php <- loaded only by request

#	example api call:
#	GET http://dev.local/unstable/dice/roll/request.json?dice_string=2d4+4+9+2d6&format=irc # this is the actual string, be sure to call url_encode() around it
#	GET http://dev.local/unstable/dice/roll/request.json?dice_string=2d4%2B4%2B9%2B2d6&format=irc # this is the url encoded version
#	LOAD CLASSES FROM: 	./classes/unstable
#	LOAD CLASS:		./classes/unstable/dice.php
#	PERFORM ACTION:		$dice->roll($_GET)

	require_once('config.php');

	$version = $request_url[1]; 
	if(!in_array($version, array('unstable'))) {
		header("HTTP/1.0 404 API Object Not Found");
		die();
	}

	define('VERSION', $version);

	$object = ucfirst($request_url[2]);
	$action = $request_url[3];

	$response = array();

	$request = new $object();
	$request->$action($_GET);

	
	#phpinfo(); 
?>
