<?php

$consumerKey = 'HrThdSlYXoFEMworzLyd';
$consumerSecret = 'TsKTtAJysXMNPNfSISOOyTwSryEgXQNY';

require '../vendor/autoload.php';

use OAuth\OAuth1\Service\BitBucket;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

ini_set('date.timezone', 'Europe/Amsterdam');

$uriFactory = new \OAuth\Common\Http\Uri\UriFactory();
$currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
$currentUri->setQuery('');

$serviceFactory = new \OAuth\ServiceFactory();

// We need to use a persistent storage to save the token, because oauth1 requires the token secret received before'
// the redirect (request token request) in the access token request.
$storage = new Session();
// Setup the credentials for the requests
$credentials = new Credentials(
    $consumerKey,
    $consumerSecret,
    $currentUri->getAbsoluteUri()
);
// Instantiate the BitBucket service using the credentials, http client and storage mechanism for the token
/** @var $bbService BitBucket */
$bbService = $serviceFactory->createService('Discogs', $credentials, $storage);

if (isset($_GET['oauth_token'])) {
    $token = $storage->retrieveAccessToken('Discogs');

    $bbService->requestAccessToken(
        $_GET['oauth_token'],
        $_GET['oauth_verifier'],
        $token->getRequestTokenSecret()
    );

    header("Location: /");
    exit();
}

$isAuthorized = true;
try {
    $token = $storage->retrieveAccessToken('Discogs');
} catch (\OAuth\Common\Storage\Exception\TokenNotFoundException $e) {
    $isAuthorized = false;
}

if (! $isAuthorized) {
    $token = $bbService->requestRequestToken();
    $url = $bbService->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
    header('Location: ' . $url);
}

$client = Discogs\ClientFactory::factory([]);
$oauth = new GuzzleHttp\Subscriber\Oauth\Oauth1([
    'consumer_key'    => $consumerKey, // from Discogs developer page
    'consumer_secret' => $consumerSecret, // from Discogs developer page
    'token'           => $token->getRequestToken(), // get this using a OAuth library
    'token_secret'    => $token->getRequestTokenSecret() // get this using a OAuth library
]);
$client->getHttpClient()->getEmitter()->attach($oauth);

$response = $client->search([
    'q' => 'searchstring'
]);
echo '<pre>';
print_r($response);
