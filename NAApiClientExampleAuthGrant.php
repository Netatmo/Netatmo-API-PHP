<?php
require_once 'NAApiClient.php';

define('CLIENT_ID', 'yoursecret');
define('CLIENT_SECRET', 'yoursecret');

$client = new NAApiClient(array("client_id" => CLIENT_ID, "client_secret" => CLIENT_SECRET));


//Test if code is provided in get parameters (that means user has already accepted the app and has been redirected here)
if(isset($_GET["code"]))
{
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
}
else
{
    if(isset($_GET["error"]))
    {
        if($_GET["error"] == "access_denied")
            echo "You refused to let application access your netatmo data\n";
        else
            echo "An error happend\n";
    }
    else
    {
        //Ok redirect to Netatmo Authorize URL
        $redirect_url = $client->getAuthorizeUrl();
        header("HTTP/1.1 ". OAUTH2_HTTP_FOUND);
        header("Location: " . $redirect_url);
        die();
    }
}
?>
