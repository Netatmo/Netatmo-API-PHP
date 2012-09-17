<?php
/*
Example of how to retrieve last user indoor temperature
This script has to be hosted by your web server in order to make it work (example with authorization code grant)
*/
require_once 'NAApiClient.php';

$client_id = "YOUR_CLIENT_ID";
$client_secret = "YOUR_CLIENT_SECRET";
$client = new NAApiClient(array("client_id" => $client_id, "client_secret" => $client_secret));


//Test if code is provided in get parameters (that means user has already accepted the app and has been redirected here)
if(isset($_GET["code"]))
{
    try
    {
        $tokens = $client->getAccessToken();        
        $refresh_token = $tokens["refresh_token"]; /*permanent refresh token that is needed to obtain new access_token*/
        $access_token = $tokens["access_token"]; /*access token that is need to access api method*/
       
        //Retrieve user device list
        try
        {
            $deviceList = $client->api("devicelist");        
            if(isset($deviceList["body"]) && isset($deviceList["body"]["devices"]) && isset($deviceList["body"]["devices"][0]))
            {
                $device_id = $deviceList["body"]["devices"][0]["_id"];//we use first user device in this example
                //Retrieve last measures info :
                $params = array("scale" => "max", "type" => "Temperature", "date_end" => "last", "device_id" => $device_id);
                $res = $client->api("getmeasure", "POST", $params);
                if(isset($res["body"][0]) && isset($res["body"][0]["beg_time"]))
                {
                    $time = $res["body"][0]["beg_time"];
                    $temperature = $res["body"][0]["value"][0][0];  
                    echo "User last temperature is $temperature Celcius @".date('c', $time)."\n";
                }
                else
                {
                    echo "No data retrieved from last measures\n";
                }
            }
            else
            {
                echo "User does not have any devices associated with him\n";
            }
        }
        catch(NAClientException $ex)
        {
            echo "User does not have any devices\n";
        }
 
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
