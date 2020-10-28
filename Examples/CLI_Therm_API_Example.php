#!/usr/bin/php
<?php
/**
* Example of Netatmo Thermostat API
* For further information, please take a look at https://dev.netatmo.com/doc
*/
define('__ROOT__', dirname(dirname(__FILE__)));
require_once (__ROOT__.'/src/Netatmo/autoload.php');
require_once ('Config.php');
require_once ('Utils.php');

$scope = Netatmo\Common\NAScopes::SCOPE_READ_THERM." " .Netatmo\Common\NAScopes::SCOPE_WRITE_THERM;

//Client configuration from Config.php
$conf = array("client_id" => $client_id,
              "client_secret" => $client_secret,
              "username" => $test_username,
              "password" => $test_password,
              "scope" => $scope);
$client = new Netatmo\Clients\NAThermApiClient($conf);

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

//Retrieve User's thermostats info:
try
{
    $thermData = $client->getData();
}
catch(Netatmo\Exceptions\NAClientException $ex)
{
    handleError("An error occured while retrieve thermostat data: " . $ex->getMessage() . "\n", TRUE);
}

if(count($thermData['devices']) === 0)
    echo ("You don't have any thermostats linked to your account. \n");
else
{
    printMessageWithBorder("Thermostats Basic Information");

    //We'll use the first device later
    $device = $thermData['devices'][0];
    $module_id = $device['modules'][0]['_id'];
    // keep record of current program and mode to restore it
    $currentProgram = getCurrentProgram($device["modules"][0]);
    list($initialMode, $initialTemp, $initialEndtime) = getCurrentMode($device["modules"][0]);

    //first print devices information
    foreach($thermData['devices'] as $dev)
    {
        printThermBasicInfo($dev);
    }



    //Create a new schedule
    $scheduleName = "testSchedule";
    //build schedule's zones & timetable
    $zones = array(
             array("type" => Netatmo\Common\NAThermZone::THERMOSTAT_SCHEDULE_SLOT_DAY,
                   "id" => 0,
                   "temp" => 19),
             array("type" => Netatmo\Common\NAThermZone::THERMOSTAT_SCHEDULE_SLOT_NIGHT,
                   "id" => 1,
                  "temp" => 17),
             array("type" => Netatmo\Common\NAThermZone::THERMOSTAT_SCHEDULE_SLOT_AWAY,
                  "id" => 2,
                  "temp" => 12),
            array("type" => Netatmo\Common\NAThermZone::THERMOSTAT_SCHEDULE_SLOT_HG,
                  "id" => 3,
                  "temp" => 7),
            array("type" => Netatmo\Common\NAThermZone::THERMOSTAT_SCHEDULE_SLOT_ECO,
                  "id" => 4,
                  "temp" => 16),
        );

    // weekly timetable = When to use which zone (offset in min + if of the zone)
    $timetable = array(
            // monday
            array("m_offset" => 0, "id" => 1),
            array("m_offset" => 420, "id" => 0),
            array("m_offset" => 480, "id" => 4),
            array("m_offset" => 1140, "id" => 0),
            array("m_offset" => 1320, "id" => 1),
            // tuesday
            array("m_offset" => 1860, "id" => 0),
            array("m_offset" => 1920, "id" => 4),
            array("m_offset" => 2580, "id" => 0),
            array("m_offset" => 2760, "id" => 1),
            // Wednesday
            array("m_offset" => 3300, "id" => 0),
            array("m_offset" => 3360, "id" => 4),
            array("m_offset" => 4020, "id" => 0),
            array("m_offset" => 4200, "id" => 1),
            // Thursday
            array("m_offset" => 4740, "id" => 0),
            array("m_offset" => 4800, "id" => 4),
            array("m_offset" => 5460, "id" => 0),
            array("m_offset" => 5640, "id" => 1),
            // Friday
            array("m_offset" => 6180, "id" => 0),
            array("m_offset" => 6240, "id" => 4),
            array("m_offset" => 6900, "id" => 0),
            array("m_offset" => 7080, "id" => 1),
            // Saturday
            array("m_offset" => 7620, "id" => 0),
            array("m_offset" => 8520, "id" => 1),
            // Sunday
            array("m_offset" => 9060, "id" => 0),
            array("m_offset" => 9960, "id" => 1)
        );
    try
    {
        $res = $client->createSchedule($device['_id'], $module_id, $zones, $timetable, $scheduleName);
        $schedule_id = $res['schedule_id'];
        printMessageWithBorder("New test Schedule created");
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        handleError("An error occured while creating a new schedule: " . $ex->getMessage() . "\n", TRUE);
    }

    //Switch to our new schedule
    if(!is_null($schedule_id))
    {
        try
        {
            $client->switchSchedule($device['_id'], $module_id, $schedule_id);
        }
        catch(Netatmo\Exceptions\NAClientException $ex)
        {
            handleError("An error occured while changing the device schedule: " . $ex->getMessage(). "\n", TRUE);
        }
        printMessageWithBorder("Schedule changed to testSchedule");
    }

    //Let's set this thermostat to away mode
    try
    {
        $client->setToAwayMode($device['_id'], $module_id);
        printMessageWithBorder($device['station_name'] . " is now in Away Mode");

    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        handleError("An error occured while setting the thermostat to away mode: ".$ex->getMessage()."\n");
    }

    //Get daily measurements of the last 30 days
    $type = "temperature,max_temp,min_temp";
    try
    {
        $measurements = $client->getMeasure($thermData['devices'][0]['_id'], $thermData['devices'][0]['modules'][0]['_id'], "1day", $type, time()-3600*24*30, time(), 30, FALSE, FALSE);
        printMeasure($measurements, $type, $device['place']['timezone'], "Daily Measurements of the last 30 days");

    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        handleError("An error occured while retrieving measures: " . $ex->getMessage(). "\n");
    }


    //Rename test schedule
    try
    {
        $client->renameSchedule($device['_id'], $module_id, $schedule_id, "To be deleted");
        printMessageWithBorder("Schedule renamed");
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        handleError("An error occured while renaming schedule $schedule_id: " . $ex->getMessage() ."\n");
    }

    //switch back to previous program
    if(!is_null($currentProgram))
    {
        try
        {
            $client->switchSchedule($device['_id'], $module_id, $currentProgram);
            printMessageWithBorder("Switching back to the original schedule");
            sleep(30); //wait for thermostat to reconnect so that the change will be effective and initial schedule will be set back
        }
        catch(Netatmo\Exceptions\NAClientException $ex)
        {
            handleError("An error occured while switching back to original schedule: ". $ex->getMessage() ."\n");
        }
    }
    // let's remove test schedule
    try
    {
        $client->deleteSchedule($device['_id'], $module_id, $schedule_id);
        printMessageWithBorder("test Schedule deleted");
        sleep(20);
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        handleError("An error occured while removing schedule $schedule_id " . $ex->getMessage() ."\n");
    }

    //let's change to a manual set point : 19°C for 10 min
    try
    {
        $client->setToManualMode($device["_id"], $module_id, 19, time() + 10 * 60);
        printMessageWithBorder("Manual setpoint set");

    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        handleError("An error occured while setting a manual setpoint ". $ex->getMessage() . "\n");
    }

    //Finally set back to the intial mode
    try
    {
        switch($initialMode)
        {
            case "away" : $client->setToAwayMode($device["_id"], $module_id, $initialEndtime);
                break;
            case "manual" : $client->setToManualMode($device["_id"], $module_id, $initialTemp, $initialEndtime);
                break;
            case "program": $client->setToProgramMode($device["_id"], $module_id);
                break;
            case "frost_guard": $client->setToFrostGuardMode($device["_id"], $module_id, $initialEndtime);
                break;
            case "off":   $client->turnOff($device["_id"], $module_id);
                break;
            case "max": $client->setToMaxMode($device["_id"], $module_id, $initialEndtime);
                break;
        }
        printMessageWithBorder("Back to original mode: " . $initialMode);
        sleep(20); //wait for thermostat synchronization to make sure, it's back in its initial mode
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        handleError("An error occured while setting back the initial mode: ". $ex->getMessage() . " \n");
    }
}
?>
