<?php
require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../environment.local.php';
session_start();

$provider = new League\OAuth2\Client\Provider\Facebook([
    'clientId'          => RINDOW_TEST_FACEBOOK_CLIENTID,
    'clientSecret'      => RINDOW_TEST_FACEBOOK_CLIENTSECRET,
    'redirectUri'  => 'http://localhost/users/delegate/confirm/facebook',
    'graphApiVersion'   => 'v2.8',
]);


if (!empty($_GET['error'])) {

    // Got an error, probably user denied access
    exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));

} elseif (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl([
        //'scope' => ['email', '...', '...'],
        //'scope' => ['email'],
    ]);
    $_SESSION['oauth2state'] = $provider->getState();

    header('Location: ' . $authUrl);
    //echo '<a href="'.$authUrl.'">Log in with Facebook!</a>';
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    echo 'Invalid state.';
    exit;

}

// Try to get an access token (using the authorization code grant)
$token = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

echo '<pre>';
// Use this to interact with an API on the users behalf
var_dump($token->getToken());
# string(217) "CAADAppfn3msBAI7tZBLWg...

// The time (in epoch time) when an access token will expire
date_default_timezone_set('Asia/Tokyo');
var_dump($token->getExpires());
var_dump(date(DATE_RFC2822,$token->getExpires()));
# int(1436825866)
echo '</pre>';

// Optional: Now you have a token you can look up a users profile data
try {

    // We got an access token, let's now get the user's details
    $user = $provider->getResourceOwner($token);

    // Use these details to create a new profile
    printf('Hello %s!', $user->getFirstName());

    echo '<pre>';
    var_dump($user);
    # object(League\OAuth2\Client\Provider\FacebookUser)#10 (1) { ...
    echo '</pre>';

} catch (\Exception $e) {

    // Failed to get user details
    exit('Oh dear...');
}

