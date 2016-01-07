<?php

/**
* Example showing how to manually subscribe to Netatmo Welcome Webhooks notifications.
* For further information regarding the webhook system, see https://dev.netatmo.com/doc/webhooks
*/

define('__ROOT__', dirname(dirname(__FILE__)));
require_once (__ROOT__.'/src/Netatmo/autoload.php');
require_once 'Config.php';
require_once 'Utils.php';

//API client configuration
$config = array("client_id" => $client_id,
            "client_secret" => $client_secret,
            "username" => $test_username,
            "password" => $test_password,
            "scope" => Netatmo\Common\NAScopes::SCOPE_READ_CAMERA);
$client = new Netatmo\Clients\NAWelcomeApiClient($config);

//Retrieve access token
try
{
    $tokens = $client->getAccessToken();
}
catch(Netatmo\Exceptions\NAClientException $ex)
{
    $error_msg = "An error happened  while trying to retrieve your tokens \n" . $ex->getMessage() . "\n";
    handleError($error_msg, TRUE);
}

// Adding/droping Webhooks for the current user
try
{
    //Adding a Webhook for your app for the current user
    $client->subscribeToWebhook(""); //insert the URL of your webhook endpoint here

    //Droping your webhook notification for the current user
    $client->dropWebhook();
}
catch(Netatmo\Exceptions\NAClientException $ex)
{
    echo "An error occured while trying to subscribe to a webhook";
    die();
}

?>
