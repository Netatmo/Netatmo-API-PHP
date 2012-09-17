<?php
/*
Authentication to Netatmo Server with the user credentials grant
*/
require_once 'NAApiClient.php';

$client_id = "YOUR_CLIENT_ID";
$client_secret = "YOUR_CLIENT_SECRET";
$username = "YOUR_MAIL";
$password = "YOUR_PWD";

$client = new NAApiClient(array("client_id" => $client_id, "client_secret" => $client_secret, "username" => $username, "password" => $password));


try
{
    $tokens = $client->getAccessToken();        
    $refresh_token = $tokens["refresh_token"]; /*permanent refresh token that is needed to obtain new access_token*/
    $access_token = $tokens["access_token"]; /*access token that is need to access api method*/
    
    //For instance retrieve user info :
    $user = $client->api("getuser", "POST");
    echo "Hello ".$user["body"]["mail"]."\n";
}
catch(NAClientException $ex)
{
    echo "An error happend while trying to retrieve your tokens\n";
}
?>
