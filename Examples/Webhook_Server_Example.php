<?php

define('__ROOT__', dirname(dirname(__FILE__)));
require_once 'Config.php';

/**
* Webhooks Endpoint Example.
* This script has to be hosted on a webserver in order to make it work.
* This endpoint should first be registered as webhook URI in your app settings on Netatmo Developer Platform (or registered using the API).
* If you don't known how to register a webhook, or simply need ore information please refer to documentation: https://dev.netatmo.com/doc/webhooks)
*/


//Get the post JSON sent by Netatmo servers
$jsonData = file_get_contents("php://input");

//Each time a webhook notification is sent, log it into a text file
if(!is_null($jsonData) && !empty($jsonData))
{
    //first check the data sent using its signature (contained in X-Netatmo-secret HTTP header). $client_secret corresponds to your application secret in Config.php
    if(hash_hmac("sha256", $jsonData, $client_secret) !== $_SERVER['HTTP_X_NETATMO_SECRET'])
    {
        //be careful on the way you handle issues, if you send back too many error codes, webhooks to your app would be suspended for a day
        trigger_error('An error occured while checking webhooks data signature', E_USER_WARNING);
    }
    else
    {
        //webhooks notifications are json encoded, you need to first decode them in order to access it as PHP arrays
        $notif = json_decode($jsonData, TRUE);

        //Printing the notification message in a file. If you want to access other available webhooks fields please see https://dev.netatmo.com/doc/webhooks/webhooks_camera
        if(isset($notif['message']))
        {
            file_put_contents('/tmp/Webhooks_examples.txt', $notif['message']. "\n", FILE_APPEND);
        }
    }
}

?>
