#!/usr/bin/php
<?php
/**
* Example of Netatmo Welcome API
* For further information, please take a look at https://dev.netatmo.com/doc
*/
define('__ROOT__', dirname(dirname(__FILE__)));
require_once (__ROOT__.'/src/Netatmo/autoload.php');
require_once ('Config.php');
require_once ('Utils.php');


$scope = Netatmo\Common\NAScopes::SCOPE_READ_CAMERA;

//Client configuration from Config.php
$conf = array("client_id" => $client_id,
              "client_secret" => $client_secret,
              "username" => $test_username,
              "password" => $test_password,
              "scope" => $scope);
$client = new Netatmo\Clients\NAWelcomeApiClient($conf);

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

//Try to retrieve user's Welcome information
try
{
    //retrieve every user's homes and their last 10 events
    $response = $client->getData(NULL, 10);
    $homes = $response->getData();
}
catch(Netatmo\Exceptions\NASDKException $ex)
{
    handleError("An error happened while trying to retrieve home information: ".$ex->getMessage() ."\n", TRUE);
}

if(is_null($homes) ||  empty($homes))
{
    handleError("No home found for this user...", TRUE);
}
printMessageWithBorder("User's Homes");
foreach($homes as $home)
{
    printHomeInformation($home);
}

$home = $homes[0];
$tz = $home->getTimezone();
$persons = $home->getPersons();

if(!empty($persons))
{
    $known = $home->getKnownPersons();
    $person = $known[0];
    //retrieve last every events that happened after the last time person has been seen
    try
    {
        $response = $client->getLastEventOf($home->getId(), $person->getId());
        $eventList = $response->getData();
    }
    catch(Netatmo\Exceptions\NASDKException $ex)
    {
        handleError("An error occured while retrieving last event of ".$person->getPseudo() . "\n");
    }
    if(!empty($eventList))
    {
        printMessageWithBorder("Events until last time ".$person->getPseudo(). " was seen");

        foreach($eventList as $event)
        {
            printEventInformation($event, $tz);
        }
        // let's retrieve 10 events that happens right before last event of the given person
        $lastIndex = count($eventList) -1;
        $lastEvent = $eventList[$lastIndex];
        $event = $eventList[0];
        try
        {
            $response = $client->getNextEvents($home->getId(), $lastEvent->getId(), 10);
            $data = $response->getData();
        }
        catch(Netatmo\Exceptions\NASDKException $ex)
        {
            handleError("An error occured while retrieving events: ". $ex->getMessage(). "\n");
        }

        if(!empty($data))
        {
            printMessageWithBorder("The 10 events that happened right before ". $person->getPseudo()." was seen");
            foreach($data as $event)
            {
                printEventInformation($event, $tz);
            }
        }

        try
        {
            $snapshot = $event->getSnapshot();
            if(!is_null($snapshot))
            {
                printMessageWithBorder("Event's snapshot");
                echo $snapshot . "\n";
            }
        }
        catch(Netatmo\Exceptions\NASDKException $ex)
        {
            handleError("An error occured while retrieving event's snapshot: ". $ex->getMessage()."\n");
        }
    }
}
?>
