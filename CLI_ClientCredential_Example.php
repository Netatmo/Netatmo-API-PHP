#!/usr/bin/php
<?php
/*
Authentication to Netatmo Server with the user credentials grant
*/
require_once 'NAApiClient.php';
require_once 'Config.php';

$client = new NAApiClient(array("client_id" => $client_id, "client_secret" => $client_secret, "username" => $test_username, "password" => $test_password));
$helper = new NAApiHelper();

try {
    $tokens = $client->getAccessToken();        
    
} catch(NAClientException $ex) {
    echo "An error happend while trying to retrieve your tokens\n";
    exit(-1);
}

// Retrieve User Info :
$user = $client->api("getuser", "POST");
echo ("-------------\n");
echo ("- User Info -\n");
echo ("-------------\n");
//print_r($user);
echo ("OK\n");
echo ("---------------\n");
echo ("- Device List -\n");
echo ("---------------\n");
$devicelist = $client->api("devicelist", "POST");
$devicelist = $helper->SimplifyDeviceList($devicelist);
//print_r($devicelist);
echo ("OK\n");
echo ("-----------------\n");
echo ("- Last Measures -\n");
echo ("-----------------\n");
$mesures = $helper->GetLastMeasures($client,$devicelist);
print_r($mesures);
echo ("OK\n");


?>
