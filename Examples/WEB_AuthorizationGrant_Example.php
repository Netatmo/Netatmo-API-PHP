<?php
/*
* Authentication to Netatmo Servers using Authorization grant.
* This script has to be hosted on a webserver in order to make it work
* For more details about Netatmo API, please take a look at https://dev.netatmo.com/doc
*/
define('__ROOT__', dirname(dirname(__FILE__)));
require_once (__ROOT__.'/src/Netatmo/autoload.php');
require_once 'Config.php';
require_once 'Utils.php';

//API client configuration
$config = array("client_id" => $client_id,
                "client_secret" => $client_secret,
                "scope" => Netatmo\Common\NAScopes::SCOPE_READ_STATION);
$client = new Netatmo\Clients\NAWSApiClient($config);

//if code is provided in get param, it means user has accepted your app and been redirected here
if(isset($_GET["code"]))
{
    //get the tokens, you can store $tokens['refresh_token'] in order to quickly retrieve a new access_token next time
    try{
        $tokens = $client->getAccessToken();
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        echo "An error occured while trying to retrieve your tokens \n";
        echo "Reason: ".$ex->getMessage()."\n";
        die();
    }
    //retrieve user's weather station data
    try{
        $data = $client->getData();
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
       echo "An error occured while retrieving data: ". $ex->getMessage()."\n";
       die();
    }

    if(!isset($data['devices']) || !is_array($data['devices']) || count($data['devices']) < 1)
    {
        echo "User has no devices \n";
        die();
    }

    //In this example, we will only deal with the first weather station linked to the user account
    $device = $data['devices'][0];

    //last month report
    $reports = getMonthReport($device, $client);

    //Get device timezone
    if(!is_null($device) && !empty($device))
    {
        if(isset($device['place']['timezone']))
            $tz = $device['place']['timezone'];
        else $tz = 'GMT';

    //print data
?>
<html>
<meta charset="UTF-8">
    <body>
        <div style = "text-align:center;">
            <h2><?php echo $device['station_name']; ?> </h2>
            <p> id : <?php echo $device['_id']; ?></p>
<?php
printDashboardDataInHtml($device['dashboard_data'], $tz);
foreach($device['modules'] as $module)
{
    printModuleInHtml($module, $tz);
}

    if(!is_null($reports) && !empty($reports))
    {
         echo "<div style= 'display:inline-block; vertical-align:top; text-align:center; margin:15px;'>";
         echo "<h3> Month report </h3>";
         foreach($reports as $report)
         {
             if(is_array($report))
                 printDashBoardDataInHtml($report, $tz);
             else echo "<p>".$report."</p>";
         }
         echo "</div>";
    }
 ?>
        </div>
    </body>
</html>
<?php
    }
}
else {
    // OAuth returned an error
    if(isset($_GET['error']))
    {
        if($_GET['error'] === "access_denied")
            echo " You refused to let this application access your Netatmo data \n";
        else echo "An error occured";
    }
    //user clicked on start button => redirect to Netatmo OAuth
    else if(isset($_GET['start']))
    {
        //Ok redirect to Netatmo Authorize URL
        $redirect_url = $client->getAuthorizeUrl();
        header("HTTP/1.1 ". 302);
        header("Location: " . $redirect_url);
        die();
    }
    // Homepage : start button
    else
    {
?>
<html>
    <body>
       <form method="GET" action="<?php echo $client->getRequestUri();?>">
           <input type='submit' name='start' value='Start'/>
       </form>
    </body>
</html>
<?php
    }
}

/**
* @param array data : dashboard_data of a device or module
* @param string $tz : timezone
* @brief : print general data of a device in HTML
*/
function printDashboardDataInHtml($data, $tz)
{
    foreach($data as $key => $value)
    {
        echo "<p>";
        echo $key.": ";
        if($key === 'time_utc' || preg_match("/^date_.*/", $key))
        {
            printTimeInTz($value, $tz, 'j F H:i');
        }
        else {
            echo $value;
            printUnit($key);
        }
        echo"</p>";
    }
}

/**
* @param array $module
* @param string $tz device's timezone
* @brief print a Netatmo Weather Station's module in HTML
*/
function printModuleInHtml($module, $tz)
{
    echo "<div style = 'display:inline-block; vertical-align:top; margin:15px; text-align:center;'>";
    echo "<h3>". $module['module_name']. "</h3>";
    echo "<p> id: ".$module['_id']. "</p>";
    echo "<p> type: ";
    switch($module['type'])
    {
        case "NAModule1" : echo "Outdoor";
            break;
        case "NAModule2" : echo "Wind Sensor";
            break;
        case "NAModule3" : echo "Rain Gauge";
            break;
        case "NAModule4" : echo "Indoor";
            break;
    }
    echo "</p>";
    printDashboardDataInHtml($module['dashboard_data'], $tz);
    echo "</div>";
}

/**
* @param array $device
* @param NAWSApiClient $client
* @return array $report : array with device or module ids as keys, and their data of the month as  values
* @brief retrieve month data for a device and its modules
*/
function getMonthReport($device, $client)
{
    $report = array();
    //step between two measurements
    $scale = '1month';
    //type of measures wanted
    $type = "Temperature,CO2,Humidity,Pressure,Noise,max_temp,date_max_temp,min_temp,date_min_temp,max_hum,date_max_hum,min_hum,date_min_hum,max_pressure,date_max_pressure,min_pressure,date_min_pressure,max_noise,date_max_noise,min_noise,date_min_noise,max_co2,date_max_co2,min_co2,date_min_co2";
    // main device
    try{
        $measure = $client->getMeasure($device['_id'], NULL, $scale, $type, NULL, "last", NULL, FALSE, FALSE);
        $measure = addMeasureKeys($measure, $type);
        $report[$device['_id']] = $measure;
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        $report[$device['_id']] = "Error retrieving measure for ".$device['_id'].": " .$ex->getMessage();
    }

    foreach($device['modules'] as $module)
    {
        switch($module['type'])
        {
            //Outdoor
            case "NAModule1": $type = "temperature,humidity";
                 break;
            //Wind Sensor
            case "NAModule2": $type = "WindStrength,WindAngle,GustStrength,GustAngle,date_max_gust";
                 break;
            //Rain Gauge
            case "NAModule3": $type = "sum_rain";
                break;
            // Indoor
            case "NAModule4": $type = "temperature,Co2,humidity,noise,pressure";
                break;
        }
        try{
            $measure = $client->getMeasure($device['_id'], $module['_id'], $scale, $type, NULL, "last", NULL, FALSE, FALSE);
            $measure = addMeasureKeys($measure, $type);
            $report[$module['_id']] = $measure;
        }
        catch(Netatmo\Exceptions\NAClientException $ex)
        {
            $report[$module['_id']] = "Error retrieving measure for " .$module['_id']. ": ".$ex->getMessage();
        }
    }
    return $report;
}
/**
* @param array measures : measure sent back by getMeasure method
* @param string $type : types of measure requested
* @return array $measurements : array of measures mapped by type
* @brief add the type as a key for each measure
*/
function addMeasureKeys($measures, $type)
{
    $measurements = array();
    $keys = explode(",", $type);
    foreach($measures as $measure)
    {
           foreach($measure as $key => $val)
           {
               $measurements[$keys[$key]] = $val;
           }
    }
    return $measurements;
}
?>
