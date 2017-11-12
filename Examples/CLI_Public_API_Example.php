#!/usr/bin/php
<?php
/**
* Example of Netatmo Public API
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

//Authenticate & Authorize
try {
	$tokens = $client->getAccessToken();
} catch(Netatmo\Exceptions\NAClientException $ex) {
	$error_msg = "An error happened  while trying to retrieve your tokens\n" . $ex->getMessage() . "\n";
	handleError($error_msg, TRUE);
}

//Initialize public API with same authentication
$public = new Netatmo\Clients\NAPublicApiClient($tokens);

$NE = [ 'lat' => 50.5, 'lon' => 4.7 ];
$SW = [ 'lat' => 50.4, 'lon' => 4.6 ];
try {
	$publicData = $public->getData($SW['lat'], $NE['lat'], $SW['lon'], $NE['lon'], 'temperature', 'true');
} catch(Netatmo\Exceptions\NAClientException $ex) {
	handleError("An error occured while retrieving public data: " . $ex->getMessage() . "\n", TRUE);
}

// Parse and compute average temperature
if ($publicData['status'] == 'ok') {
	echo '*** '.count($publicData['body']).' public results ***'.PHP_EOL;
	
	$temp = 0;
	$count = 0;
	foreach ($publicData['body'] as $body) {
		foreach ($body['measures'] as $measures) {
			if (isset($measures['type'][0]) && $measures['type'][0] == 'temperature') {
				echo $count.': '.array_values($measures['res'])[0][0].PHP_EOL;
				$temp += array_values($measures['res'])[0][0];
				$count++;
			}
			if (isset($measures['type'][1]) && $measures['type'][1] == 'temperature') {
				echo $count.': '.array_values($measures['res'])[0][1].PHP_EOL;
				$temp += array_values($measures['res'])[0][1];
				$count++;
			}
		}
	}
	$temp = $temp / $count;
	echo '*** Average temperature: '.$temp.PHP_EOL;
}

?>
