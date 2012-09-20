<?php
/*
Authentication to Netatmo Server with the authorization grant
This script has to be hosted by your web server in order to make it work
*/

require_once 'NAApiClient.php';
require_once 'Config.php';

$client = new NAApiClient(array("client_id" => $client_id, "client_secret" => $client_secret));


//Test if code is provided in get parameters (that means user has already accepted the app and has been redirected here)
if(isset($_GET["code"]))
{
    try
    {
	// Get the token for later usage.
        $tokens = $client->getAccessToken();        
	$helper = new NAApiHelper();
        
	$user = $client->api("getuser", "POST");
	$devicelist = $client->api("devicelist", "POST");
	$devicelist = $helper->SimplifyDeviceList($devicelist);
	$mesures = $helper->GetLastMeasures($client,$devicelist);
	print_r($mesures);

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
