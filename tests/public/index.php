<?php
$uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if($uri=='/index')
    $uri='/';

switch ($uri) {
	case '/':
		require __DIR__.'/../controllers/index.php';
		break;
	case '/users/delegate/confirm/google':
		require __DIR__.'/../controllers/oauth2-google.php';
		break;
	case '/users/delegate/confirm/facebook':
		require __DIR__.'/../controllers/oauth2-facebook.php';
		break;
	case '/users/delegate/confirm/auth0':
		require __DIR__.'/../controllers/oauth2-auth0.php';
		break;
	
	default:
		echo 'page not found';
		break;
}
