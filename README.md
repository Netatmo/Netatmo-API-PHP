# Netatmo-API-PHP

##Updating to v2.x
This release is a major update, and might break the compatibility with older versions. If you were using an older version of our SDK, you will have to stop using NAApiClient class and use either NAThermApiClient or NAWSApiClient instead.

As a matter of fact, those are adding an additional abstraction layer which will make it easier for you to use : API call using "api" client method are now replaced with dedicated methods along with the parameters they expect for each product API.

You also might need to change the include path of the SDK files as they are now stored in the "src" folder (API clients are stored in "src/Netatmo/Clients/" folder).

### Updating to v2.1.x
This release introduced namespaces to avoid collisions with other packages, which is a breaking change as classes provided by the Netatmo SDK are no longer available in the global namespace. For instance, NAThermApiClient becomes Netatmo\Clients\NAThermApiClient. For more information, please see http://php.net/manual/en/language.namespaces.php.

It means the use of Netatmo SDK classes in the global namespace is deprecated. It is still possible to use them so far, so that it won't break your existing code. However, we recommend to update your code as soon as possible to take into account those changes.

It also introduced an autoloader respecting the PSR-4 standards, so you only need to include the src/Netatmo/autoload.php file and you're good to go.

## Install and Configure

To install the sdk, extract the downloaded files to your project directory. Then, just include the src/Netatmo/autoload.php file and instantiate a new NAWSApiClient (or NAThermApiClient, or NAWelcomeApiClient) object with your application client_id and client_secret:



    $config = array();
    $config['client_id'] = 'YOUR_APP_ID';
    $config['client_secret'] = 'YOUR_APP_SECRET';
    $config['scope'] = 'read_station read_thermostat write_thermostat';
    $client = new Netatmo\Clients\NAApiClient($config);

## Authenticate & Authorize

The SDK provides helper-methods to authenticate and authorize your app to access a user's data

### Retrieving an access token through a user credential grant

    $username = 'YOUR_USERNAME';
    $pwd = 'YOUR_PWD';
    $client->setVariable('username', $username);
    $client->setVariable('password', $pwd);
    try
    {
        $tokens = $client->getAccessToken();
        $refresh_token = $tokens['refresh_token'];
        $access_token = $tokens['access_token'];
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        echo "An error occcured while trying to retrive your tokens \n";
    }

Please note that you should NOT store users' credentials (which is not secure) and repeatedly perform user credentials authentication as your application's users will receive emails to warn them someone is using their credentials to connect to their account. Instead, you should use the refresh token sent back to you at the end of the authentication process to get a new access token each time it has expired. For more information, about access and refresh tokens, please refer to OAuth2 protocol documentation.

### Retrieving an access token through Authorization code grant

    //test if "code" is provided in get parameters (which would mean that user has already accepted the app and has been redirected here)
    if(isset($_GET['code']))
    {
        try
        {
           $tokens = $client->getAccessToken();
           $refresh_token = $tokens['refresh_token'];
           $access_token = $tokens['access_token'];
        }
        catch(Netatmo\Exceptions\NAClientException $ex)
        {
           echo " An error occured while trying to retrieve your tokens \n";
        }
    }
    else if(isset($_GET['error']))
    {
        if($_GET['error'] === 'access_denied')
            echo "You refused that this application access your Netatmo Data";
        else echo "An error occured \n";
    }
    else
    {
        //redirect to Netatmo Authorize URL
        $redirect_url = $client->getAuthorizeUrl();
        header("HTTP/1.1 ". OAUTH2_HTTP_FOUND);
        header("Location: ". $redirect_url);
        die();
    }

## Use API methods

### Netatmo Weather Station API client implementation - PHP SDK

If you need further information about how the Netatmo Weather Station works, please see https://dev.netatmo.com/dev/resources#/technical/reference/weatherstation

#### Retrieving Netatmo Weather Station's data

Once an access token has been retrieved, it can be used to retrieved user's devices data


    $data = $client->getData(NULL, TRUE);
    foreach($data['devices'] as $device)
    {
        echo $device['station_name'] . "\n";
        print_r($device['dashboard_data']);
        foreach($device['modules'] as $module)
        {
            echo $module['module_name'];
            print_r($module['dashboard_data']);
        }
    }

$data will be an array containing a list of devices along with their modules and their last data stored: First the devices belonging to the user, then user's friends devices, and finally the user's favorites devices.

To retrieve a specific device's data, you have to provide its id:

    $data = $client->getData('YOUR_DEVICE_ID');

If it is one of the user's favorite device, you'll have to set the "get_favorites" parameter to TRUE in order to retrieve it:

    $data = $client->getData('YOUR_FAV_DEVICE_ID', TRUE);

#### Retrieving Netatmo Weather Station's measurements

You can also retrieve specific measurements of a given period for a given device:

    $device = $data['devices'][0];
    $module = $device['modules'][0];
    $scale = "1day";
    $type = $module['data_type'];
    $date_begin = time() -24*3600*7; //1 week ago
    $date_end = time() //now
    $optimized = FALSE;
    $real_time = FALSE;
    $measurements = $client->getMeasure($device['_id'], $module_id, $scale, $type, $date_begin, $date_end, $optimized, $real_time);

Please note that not giving a module_id will retrieve main device measurements.
The scale parameter corresponds to the step between two measures, the type depends on the device or module data_type and the scale. For more details regarding its allowed values, please take a glance at https://dev.netatmo.com/doc/getmeasure.
Set the optimized flag to true, if you need the json response to be lighter but a little trickier to parse : you'll only have the beginning timestamp provided, and you'll have to compute the others using the scale.
Finally, the real_time flag enables to remove the timestamp offset in scales higher than 'max'. As a matter of fact, since data are aggregated timestamps are offset by + scale/2 by default.

### Netatmo Thermostat API client implementation - PHP SDK

If you need further information about how the Netatmo Thermostat works, please see https://dev.netatmo.com/dev/resources#/technical/reference/thermostat

#### Retrieving Netatmo Thermostat's data

The Thermostat API client just works the same way as the Weather Station API Client:

    $client = new Netatmo\Clients\NAThermApiClient($config);

It also has its own method to retrieve user's thermostats data which returns an array of devices along with their data

    $data = $client->getData();

In order to retrieve a specific thermostat, you need to provide its id

    $data = $client->getData($device_id);

#### Retrieving Netatmo Thermostat's measurements

Thermostat also has its getMeasure method, which works in the exact same way as the weather station one, in order to retrieve specific data of a given period:

    $device = $data['devices'][0];
    $module = $device['modules'][0];
    $scale = "1day";
    $type = "Temperature,max_temp,min_temp,date_max_temp";
    $date_begin = time() -24*3600*7; //1 week ago
    $date_end = time(); //now
    $limit = NULL;
    $optimized = FALSE;
    $real_time = FALSE;
    $measurements = $client->getMeasure($device['_id'], $module_id, $scale, $type, $date_begin, $date_end, $limit, $optimized, $real_time);


#### Sending orders to the Netatmo Thermostat

You can easily change the thermostat setpoint:

    try{
        $client->setToAwayMode($device['_id'], $module['_id']);
        $client->setToFrostGuardMode($device['_id'], $module['_id']);
        $client->setToProgramMode($device['_id'], $module['_id']);
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        "An error occured";
    }

And you can also give time-limited orders:

    $endtime = time() +2*3600;
    $client->setToAwayMode($device['_id'], $module['_id'], $endtime); // away for 2 hours
    $client->setToFrostGuardMode($device['_id'], $module['_id'], $endtime);

Some orders require a time-limit:

    $endtime = time() + 2*3600;
    $client->setToMaxMode($device['_id'], $module['_id'], $endtime);

Finally, you have the ability to just set a Manual temperature setpoint to your thermostat:

    $endtime = time() + 2*3600;
    $temp = 21.5;
    $client->setToManualMode($device['_id'], $module['_id'], $temp, $endtime);

You also have the possibility to turn off your thermostat:

    $client->turnOff($device['_id'], $module['_id']);

(To turn it back on, set another setpoint ex : order the thermostat to follow its schedule)

#### Dealing with Netatmo Thermostat's weekly schedules

The Netatmo Thermostat SDK also enables you to deal with the thermostat schedule:

You can create a new schedule:

    $res = $client->createSchedule($device['_id'], $module['_id'], $zones, $timetable, 'new schedule');

You'll find the id of the created schedule in the return value.

    $schedule_id = $res['schedule_id'];

Then switch the thermostat current schedule to another existing schedule:

    $client->switchSchedule($device['_id'], $module['_id'], $schedule_id);

And modify your current schedule:

    $client->syncSchedule($device['_id'], $module['_id'], $zones, $timetable);

Then you can rename a schedule:

     $client->renameSchedule($device['_id'], $module['_id'], $schedule_id, 'new Name');

Finally, you can also delete a schedule:

    $client->deleteSchedule($device['_id'], $module['_id'], $schedule_id);

### Netatmo Welcome API client implementation - PHP SDK

If you need more information regarding how the Netatmo Welcome works, please see https://dev.netatmo.com/dev/resources#/technical/reference/welcome

### Retrieving Netatmo Welcome data

The Netatmo Welcome SDK works in a slightly different way than Netatmo Weather Stations and Netatmo Thermostat SDK do.
First, instantiate the client:

    $client = new Netatmo\Clients\NAWelcomeApiClient($config);

The Netatmo Welcome SDK enables you to retrieve the data (persons, cameras, events) of every home belonging to an user:

    $res = $client->getData():

If you only want to retrieve information about a specific user's home, just specify its id:

    $res = client->getData($home_id);

Please note that you can limit the number of events requested per home, using the $size parameter (default value is 30):

    $res = client->getData(NULL, 10); // will retrieve every home belonging to the user and only the 10 last associated events

The difference with Weather Station and Thermostats SDK is that the client returns NAResponseHandler objects. Those enable you to either get the raw data returned by the Netatmo API as PHP arrays or to get it as array of objects (NAHome or NAEvent):

    $array = $res->getDecodedBody();
    $objects = $res->getData();


### Retrieving specific events from a given home

In addition of retrieving all information for a home, you can also get only events you're interested in. In that purpose, you can use the three following methods:

- First, you can retrieve every event until the last one of a specific person

        $res = $client->getLastEventOf($home_id, $person_id);
        $events = $res->getData();

    This function also accepts an offset parameter, which gives the abitility to retrieve a certain number of events older than the requested one. Default value is 0.

        $res = $client->getLastEventOf($home_id, $event_id, 5);
        $events = $res->getData();

- Then, you also have the ability to retrieve every events until a specific one

        $res = $client->getEventsUntil($home_id, $event_id);
        $events = $res->getData();

- Finally, you can also get events that happened before a specific one. In that purpose, you should specify how many events you want to retrieve using the $size parameter. Default value is 30.

        $res = $client->getNextEvents($home_id, $event_id, 10); // get the 10 events that happened right before event_id
        $events = $res->getData();

### Retrieving a picture linked to an event or a person face

After retrieving event's or person's information using one of the previous methods, you might want to be able to get the picture corresponding to an event snapshot or a person face. In order to do so, you will need the snapshot or face information of that event if you're dealing with raw data in PHP array:

    $img = $client->getCameraPicture($event['snapshot']);

If you are working with the objects provided by the SDK, just call the object getter on the picture, to retrieve its URL:

    $pictureURL = $event->getSnapshot();
    $faceURL = $person->getFace();

### Quick Example

    $scope = Netatmo\Common\NAScopes::SCOPE_READ_CAMERA;

    //Client configuration from Config.php
    $conf = array('client_id' => $client_id,
              'client_secret' => $client_secret,
              'username' => $test_username,
              'password' => $test_password,
              'scope' => $scope);
    $client = new Netatmo\Clients\NAWelcomeApiClient($conf);

    //Retrieve access token
    try
    {
        $tokens = $client->getAccessToken();
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        echo "An error happened  while trying to retrieve your tokens \n" . $ex->getMessage() . "\n";
    }

    //Try to retrieve user's Welcome information
    try
    {
        //retrieve every user's homes and their last 10 events
        $response = $client->getData(NULL, 10);
        $homes = $response->getData();
    }
    catch(Netatmo\Exceptions\NASDKException $ex)
    {
        echo "An error happened while trying to retrieve home information: ".$ex->getMessage() ."\n";
    }

    //print homes names
    foreach($homes as $home)
    {
        echo $home->getName() . "\n";
    }

### Webhooks for Netatmo Welcome

Your app can subscribe to Webhooks notification for Netatmo Welcome events. There's two differente way to sign up for this: either by filling in the Webhook URL in your application settings on our developer plaftorm (each user accepting the app will be concerned by webhooks notifications) or you can use the API to manage yourself webhooks registration for users:

    $client->subscribeToWebhook($webhook_url);

And for unsubscribing webhooks for an user:

    $client->dropWebhook();

## Error handling

###SDK Errors
The SDK throws NASDKException when an error happens. It encapsulates Networking errors, API errors and SDK objects error for Netatmo Welcome SDK.

    try{
        $client->getData();
    }
    catch(Netatmo\Exceptions\NASDKException $ex)
    {
        //Handle error here
        echo $ex->getMessage();
    }


### API Client errors
The client throws NAClientException if it encounters an error dealing with the API or Networking protocol:

    try{
        $client->getData();
    }
    catch(Netatmo\Exceptions\NAClientException $ex)
    {
        //Handle error here
        echo $ex->getMessage();
    }

## Details
Please read https://dev.netatmo.com/dev/resources#/technical/introduction for further information.
