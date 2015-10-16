<?php

function handleError($message, $exit = FALSE)
{
    echo $message;
    if($exit)
        exit(-1);
}

function printTimeInTz($time, $timezone, $format)
{
    try{
        $tz = new DateTimeZone($timezone);
    }
    catch(Exception $ex)
    {
        $tz = new DateTimeZone("GMT");
    }
    $date = new DateTime();
    $date->setTimezone($tz);
    $date->setTimestamp($time);
    echo $date->format($format);
}

function printBorder($message)
{
    $size = strlen($message);
    for($i = 0; $i < $size; $i++)
        echo("-");
    echo("\n");
}

function printMessageWithBorder($message)
{
    $message = "- " . $message . " -";
    printBorder($message);
    echo $message . "\n";
    printBorder($message);
}

function printMeasure($measurements, $type, $tz, $title = NULL, $monthly = FALSE)
{
    if(!empty($measurements))
    {
        if(!empty($title))
            printMessageWithBorder($title);

        if($monthly)
            $dateFormat = 'F: ';
        else $dateFormat = 'j F: ';
        //array of requested info type, needed to map result values to what they mean
        $keys = explode(",", $type);

        foreach($measurements as $timestamp => $values)
        {
            printTimeinTz($timestamp, $tz, $dateFormat);
             echo"\n";
            foreach($values as $key => $val)
            {
                echo $keys[$key] . ": ";
                if($keys[$key] === "time_utc" || preg_match("/^date_.*/", $keys[$key]))
                    echo printTimeInTz($val, $tz, "j F H:i");
                else{
                    echo $val;
                    printUnit($keys[$key]);
                }
                if(count($values)-1 === $key || $monthly)
                    echo "\n";
                else echo ", ";
            }
        }
    }
}

/**
 * function printing a weather station or modules basic information such as id, name, dashboard data, modules (if main device), type(if module)
 *
 */
function printWSBasicInfo($device)
{
    if(isset($device['station_name']))
        echo ("- ".$device['station_name']. " -\n");
    else if($device['module_name'])
        echo ("- ".$device['module_name']. " -\n");

    echo ("id: " . $device['_id']. "\n");

    if(isset($device['type']))
    {
        echo ("type: ");
        switch($device['type'])
        {
            // Outdoor Module
            case "NAModule1": echo ("Outdoor\n");
                              break;
            //Wind Sensor
            case "NAModule2": echo("Wind Sensor\n");
                              break;

            //Rain Gauge
            case "NAModule3": echo("Rain Gauge\n");
                              break;
            //Indoor Module
            case "NAModule4": echo("Indoor\n");
                              break;
            case "NAMain" : echo ("Main device \n");
                            break;
        }

    }

    if(isset($device['place']['timezone']))
        $tz = $device['place']['timezone'];
    else $tz = 'GMT';

    if(isset($device['dashboard_data']))
    {
        echo ("Last data: \n");
        foreach($device['dashboard_data'] as $key => $val)
        {
            if($key === 'time_utc' || preg_match("/^date_.*/", $key))
            {
                echo $key .": ";
                printTimeInTz($val, $tz, 'j F H:i');
                echo ("\n");
            }
            else if(is_array($val))
            {
                //do nothing : don't print historic
            }
            else {
                echo ($key .": " . $val);
                printUnit($key);
                echo "\n";
            }
        }

        if(isset($device['modules']))
        {
            echo (" \n\nModules: \n");
            foreach($device['modules'] as $module)
                printWSBasicInfo($module);
        }
    }

    echo"       ----------------------   \n";
}

function printUnit($key)
{
    $typeUnit = array('temp' => '°C', 'hum' => '%', 'noise' => 'db', 'strength' => 'km/h', 'angle' => '°', 'rain' => 'mm', 'pressure' => 'mbar', 'co2' => 'ppm');
    foreach($typeUnit as $type => $unit)
    {
        if(preg_match("/.*$type.*/i", $key))
        {
            echo " ".$unit;
            return;
        }
    }
}

/** THERM Utils function **/
/*
* @brief print a thermostat basic information in CLI
*/
function printThermBasicInfo($dev)
{
    //Device
    echo (" -".$dev['station_name']."- \n");
    echo (" id: ".$dev['_id']." \n");
    echo ("Modules : \n");
    // Device's modules info
    foreach($dev['modules'] as $module)
    {
        echo ("    - ".$module['module_name']." -\n");

        //module last measurements
        echo ("    Last Measure date : ");
        printTimeInTz($module['measured']['time'], $dev['place']['timezone'], 'j F H:i');
        echo("\n");
        echo ("    Last Temperature measured: ". $module['measured']['temperature']);
        printUnit("temperature");
        echo("\n");
        echo ("    Last Temperature setpoint: ". $module['measured']['setpoint_temp']);
        printUnit('setpoint_temp');
        echo("\n");
        echo ("    Program List: \n");

        //program list
        foreach($module['therm_program_list'] as $program)
        {
            if(isset($program['name']))
                echo ("        -".$program['name']."- \n");
            else echo("        -Standard- \n");
            echo ("        id: ".$program['program_id']." \n");
            if(isset($program['selected']) && $program['selected'] === TRUE)
            {
                echo "         This is the current program \n";
            }
        }
    }

}

/**
* @brief returns the current program of a therm module
*/
function getCurrentProgram($module)
{
    foreach($module['therm_program_list'] as $program)
    {
        if(isset($program['selected']) && $program['selected'] === TRUE)
            return $program['program_id'];
    }
    //not found
    return NULL;
}

/**
* @brief returns the current setpoint of a therm module along with its setpoint temperature and endtime if defined
*/
function getCurrentMode($module)
{
    $initialMode = $module["setpoint"]["setpoint_mode"];
    $initialTemp = isset($module["setpoint"]["setpoint_temp"]) ? $module["setpoint"]["setpoint_temp"]: NULL;
    $initialEndtime = isset($module['setpoint']['setpoint_endtime']) ? $module['setpoint']['setpoint_endtime'] : NULL;

    return array($initialMode, $initialTemp, $initialEndtime);

}

function printHomeInformation(NAHome $home)
{
    !is_null($home->getName()) ? printMessageWithBorder($home->getName()) : printMessageWithBorder($home->getId());
    echo ("id: ". $home->getId() ."\n");

    $tz = $home->getTimezone();
    $persons = $home->getPersons();
	
    if(!empty($persons))
    {
        printMessageWithBorder("Persons");
        //print person list
        foreach($persons as $person)
        {
            printPersonInformation($person, $tz);
        }
    }

    if((!empty($home->getEvents())))
    {
        printMessageWithBorder('Timeline of Events');
        //print event list
        foreach($home->getEvents() as $event)
        {
            printEventInformation($event, $tz);
        }
    }

    if(!empty($home->getCameras()))
    {
        printMessageWithBorder("Cameras");
        foreach($home->getCameras() as $camera)
        {
            printCameraInformation($camera);
        }
    }
}


function printPersonInformation(NAPerson $person, $tz)
{
    $person->isKnown() ? printMessageWithBorder($person->getPseudo()) : printMessageWithBorder("Inconnu");
    echo("id: ". $person->getId(). "\n");
    if($person->isAway())
        echo("is away from home \n" );
    else echo("is home \n");

    echo ("Last seen on: ");
    printTimeInTz($person->getLastSeen(), $tz, "j F H:i");
    echo ("\n");
}

function printEventInformation(NAEvent $event, $tz)
{
  printTimeInTz($event->getTime(), $tz, "j F H:i");
  $message = removeHTMLTags($event->getMessage());
  echo(": ".$message. "\n");
}

function printCameraInformation(NACamera $camera)
{
    !is_null($camera->getName()) ? printMessageWithBorder($camera->getName()) : printMessageWithBorder($camera->getId());

    echo("id: ". $camera->getId() ."\n");
    echo("Monitoring status: ". $camera->getVar(NACameraInfo::CI_STATUS) ."\n");
    echo("SD card status: " .$camera->getVar(NACameraInfo::CI_SD_STATUS) . "\n");
    echo ("Power status: ". $camera->getVar(NACameraInfo::CI_ALIM_STATUS) ."\n");

    if($camera->getGlobalStatus())
        $globalStatus = "OK";
    else $globalStatus = "NOK";

    echo ("Global Status: ". $globalStatus ."\n");

}

function removeHTMLTags($string)
{
   return preg_replace("/<.*?>/", "", $string);
}

?>
