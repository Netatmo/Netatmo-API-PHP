#!/usr/bin/php
<?php
/*
Authentication to Netatmo Server with the user credentials grant
*/
require_once 'NAApiClient.php';
require_once 'Config.php';

$scope = NAScopes::SCOPE_READ_THERM." ".NAScopes::SCOPE_WRITE_THERM;

$client = new NAApiClient(array("client_id" => $client_id, "client_secret" => $client_secret, "username" => $test_username, "password" => $test_password, "scope" => $scope));

try {
    $tokens = $client->getAccessToken();        
    
}
catch(NAClientException $ex) {
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
$devicelist = $client->api("devicelist", "POST", array("app_type" => "app_thermostat"));
echo ("OK\n");
if(isset($devicelist["devices"]) && isset($devicelist["devices"][0])){
    $device = $devicelist["devices"][0];
    if(isset($device["modules"]) && isset($device["modules"][0])){
        $module = $device["modules"][0];
        echo ("-----------------\n");
        echo ("- Therm State -\n");
        echo ("-----------------\n");
        $thermstate = $client->api("getthermstate", "POST", array("device_id" => $device["_id"], "module_id" => $module));
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
        $program = null;
        if(isset($thermstate["therm_program_order"])){
            echo ("-------------------------\n");
            echo ("- Pending program_order -\n");
            echo ("-------------------------\n");
            $program = $thermstate["therm_program_order"];
            print_r($thermstate["therm_program_order"]);
        }
        else if(isset($thermstate["therm_program"])){
            echo ("-------------------\n");
            echo ("- Current program -\n");
            echo ("------------------\n");
            $program = $thermstate["therm_program"];
            print_r($thermstate["therm_program"]);
        }
        echo ("OK\n");

        //lets syncschedule
        if($program){
            $res = $client->api("syncschedule", "POST", array("device_id" => $device["_id"], "module_id" => $module, "zones" => $program["zones"], "timetable" => $program["timetable"]));
            echo ("---------------------\n");
            echo ("- New program synced-\n");
            echo ("---------------------\n");
            echo ("OK\n");
        }

        //lets order a manual setpoint @19 for 2 hours
        $client->api("setthermpoint", "POST", array("device_id" => $device["_id"], "module_id" => $module, "setpoint_mode" => "manual", "setpoint_endtime" => mktime() + 2*3600, "setpoint_temp" => 19));
        echo ("------------------\n");
        echo ("- bypass ordered -\n");
        echo ("------------------\n");
        echo ("OK\n");
    }
}


?>
