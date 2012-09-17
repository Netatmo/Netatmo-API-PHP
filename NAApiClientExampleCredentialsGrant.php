<?php
require_once 'NAApiClient.php';

define('CLIENT_ID', 'yoursecret');
define('CLIENT_SECRET', 'yoursecret');

$username = "yourmail";
$password = "yourpwd";

$client = new NAApiClient(array("client_id" => CLIENT_ID, "client_secret" => CLIENT_SECRET, "username" => $username, "password" => $password));


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
