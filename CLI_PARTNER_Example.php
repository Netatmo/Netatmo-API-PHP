<?php
/*
Authentication to Netatmo Server with the user credentials grant
Show how to use the Partner API
In this example we will :
 * - retrieve the list of devices your application has access to (partnersdevice)
 * - for each device we will retrieve device information such as module_id (thermostat id in that case), battery status etc ...
 * - for each couple device/thermostat we will retrieve current state
 * - finally for each device we will set device in frost-guard mode 
*/
require_once 'NAApiClient.php';
require_once 'Config.php';


$scope = NAScopes::SCOPE_READ_THERM." ".NAScopes::SCOPE_WRITE_THERM;
$client = new NAApiClient(array("client_id" => $client_id, "client_secret" => $client_secret, "username" => $test_username, "password" => $test_password, "scope" => $scope));

/*Retrieve user access_token*/
/* This user is the user created to access your partner application */
try {
    $tokens = $client->getAccessToken();        
}
catch(NAClientException $ex) {
    echo "An error happend while trying to retrieve your tokens\n";
    echo $ex->getMessage()."\n";
    exit(-1);
}

try{
    //Retrieve all your partner devices 
    $devicelist = $client->api("partnerdevices", "POST");
    foreach($devicelist as $device_id){
        //retrieve device information from api
        $devices = $client->api("devicelist", "POST", array("app_type" => "app_thermostat", "device_id" => $device_id));
        if(isset($devices["devices"][0])){
            $device = $devices["devices"][0];
            if(isset($device["modules"]) && isset($device["modules"][0])){

                //Retrieve getthermstate
                $thermostat_id = $device["modules"][0];
                echo "Retrieving thermstate for $thermostat_id/$device_id\n";
                $thermstate = $client->api("getthermstate", "POST", array("device_id" => $device_id, "module_id" => $thermostat_id));
                if(isset($thermstate["measured"])){
                    echo ("-----------------\n");
                    echo ("- Last measures -\n");
                    echo ("-----------------\n");
                    print_r($thermstate["measured"]);
                }
                if(isset($thermstate["setpoint_order"])){
                    echo ("--------------------------\n");
                    echo ("- Pending setpoint_order -\n");
                    echo ("--------------------------\n");
                    print_r($thermstate["setpoint_order"]);
                }
                else if(isset($thermstate["setpoint"])){
                    echo ("--------------------\n");
                    echo ("- Current setpoint -\n");
                    echo ("--------------------\n");
                    print_r($thermstate["setpoint"]);
                }

                //now set an froze-guard setpoint to every devices 
                $res = $client->api("setthermpoint", "POST", array("device_id" => $device_id, "module_id" => $thermostat_id, "setpoint_mode" => "hg"));
                echo "$thermostat_id/$device_id set in froze-guard mode\n";
            }
        }
    }
}
catch(NAClientException $ex){
    echo "An error happend during process\n";
    echo $ex->getMessage()."\n";
}    

?>
