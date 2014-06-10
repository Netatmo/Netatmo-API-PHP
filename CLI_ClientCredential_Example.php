#!/usr/bin/php
<?php
/*
Authentication to Netatmo Server with the user credentials grant
*/
require_once 'NAApiClient.php';
require_once 'Config.php';

$scope = NAScopes::SCOPE_READ_STATION;

$client = new NAApiClient(array("client_id" => $client_id, "client_secret" => $client_secret, "username" => $test_username, "password" => $test_password, "scope" => $scope));
$helper = new NAApiHelper($client);

try {
    $tokens = $client->getAccessToken();

} catch(NAClientException $ex) {
    echo "An error happend while trying to retrieve your tokens\n";
    exit(-1);
}

// Retrieve User Info :
$user = $helper->api("getuser", "POST");
echo ("-------------\n");
echo ("- User Info -\n");
echo ("-------------\n");
//print_r($user);
echo ("OK\n");
echo ("---------------\n");
echo ("- Device List -\n");
echo ("---------------\n");
$devicelist = $helper->simplifyDeviceList();
echo ("OK\n");
echo ("-----------------\n");
echo ("- Last Measures -\n");
echo ("-----------------\n");
$mesures = $helper->getLastMeasures();
print_r($mesures);
echo ("OK\n");
echo ("---------------------\n");
echo ("- Last Day Measures -\n");
echo ("---------------------\n");
$mesures = $helper->getAllMeasures(mktime() - 86400);
print_r($mesures);
echo ("OK\n");


?>
