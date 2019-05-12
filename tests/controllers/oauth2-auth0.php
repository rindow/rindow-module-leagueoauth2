<?php
require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../environment.local.php';
session_start();

$provider = new Rindow\Module\LeagueOAuth2\Client\Provider\Auth0([
    'domain'       => RINDOW_TEST_AUTH0_DOMAIN,
    'clientId'     => RINDOW_TEST_AUTH0_CLIENTID,
    'clientSecret' => RINDOW_TEST_AUTH0_CLIENTSECRET,
    'audience'     => RINDOW_TEST_AUTH0_AUDIENCE, // When you want the JWT.
    'redirectUri'  => 'http://localhost/users/delegate/confirm/auth0',
    'scopes' => ['openid','email','profile'],
]);

if (!empty($_GET['error'])) {

    // Got an error, probably user denied access
    exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));

} elseif (empty($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();

//echo '<pre>';
//echo 'login'."\n";
//var_dump($authUrl);
//echo '</pre>';
//exit;


    header('Location: ' . $authUrl);
    exit;

} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    // State is invalid, possible CSRF attack in progress
    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Use this to interact with an API on the users behalf
    echo 'Token:';
    echo $token->getToken();
    echo '<br>';

    // Use this to get a new access token if the old one expires
    echo 'RefreshToken:';
    echo $token->getRefreshToken();
    echo '<br>';

    // Number of seconds until the access token will expire, and need refreshing
    date_default_timezone_set('Asia/Tokyo');
    echo 'Expires:';
    echo date(DATE_RFC2822,$token->getExpires());
    echo '<br>';

    echo 'ResourceOwnerId:';
    echo $token->getResourceOwnerId();
    echo '<br>';

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the owner details
        $ownerDetails = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        echo 'Id:'.$ownerDetails->getId().'<br>';
        echo '<pre>';
        var_dump($ownerDetails->toArray());
        echo '</pre>';

    } catch (Exception $e) {

        // Failed to get user details
        exit('Something went wrong: ' . $e->getMessage());

    }
}
