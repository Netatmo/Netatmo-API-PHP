<?php
require_once 'AppliCommonPublic.php';

define('CURL_ERROR_TYPE', 0);
define('API_ERROR_TYPE',1);//error return from api
define('INTERNAL_ERROR_TYPE', 2); //error because internal state is not consistent
define('JSON_ERROR_TYPE',3);
define('NOT_LOGGED_ERROR_TYPE', 4); //unable to get access token

define('BACKEND_BASE_URI', "http://192.168.0.44/");
define('BACKEND_SERVICES_URI', "http://192.168.0.44/api");
define('BACKEND_ACCESS_TOKEN_URI', "http://192.168.0.44/oauth2/token");
define('BACKEND_AUTHORIZE_URI', "http://192.168.0.44/oauth2/authorize");

/*
define('BACKEND_BASE_URI', "http://api.netatmo.net/");
define('BACKEND_SERVICES_URI', "http://api.netatmo.net/api");
define('BACKEND_ACCESS_TOKEN_URI', "https://api.netatmo.net/oauth2/token");
define('BACKEND_AUTHORIZE_URI', "https://api.netatmo.net/oauth2/authorize");
*/

/**
 * OAuth2.0 Netatmo exception handling
 *
 * @author Originally written by Thomas Rosenblatt <thomas.rosenblatt@netatmo.com>.
 */
class NAClientException extends Exception
{
    public $error_type;
    /**
    * Make a new API Exception with the given result.
    *
    * @param $result
    *   The result from the API server.
    */
    public function __construct($code, $message, $error_type)
    {
        $this->error_type = $error_type;
        parent::__construct($message, $code);
    }
}


class NAApiErrorType extends NAClientException
{
    public $http_code;
    public $http_message;
    public $result;
    function __construct($code, $message, $result)
    {
        $this->http_code = $code;
        $this->http_message = $message;
        $this->result = $result;
        if(isset($result["error"]) && is_array($result["error"]) && isset($result["error"]["code"]))
        {
            parent::__construct($result["error"]["code"], $result["error"]["message"], API_ERROR_TYPE);
        }
        else
        {
            parent::__construct($code, $message, API_ERROR_TYPE);
        }
    }
}

class NACurlErrorType extends NAClientException
{
    function __construct($code, $message)
    {
        parent::__construct($code, $message, CURL_ERROR_TYPE);
    }
}

class NAJsonErrorType extends NAClientException
{
    function __construct($code, $message)
    {
        parent::__construct($code, $message, JSON_ERROR_TYPE);
    }
}

class NAInternalErrorType extends NAClientException
{
    function __construct($message)
    {
        parent::__construct(0, $message, INTERNAL_ERROR_TYPE);
    }
}

class NANotLoggedErrorType extends NAClientException
{
    function __construct($code, $message)
    {
        parent::__construct($code, $message, NOT_LOGGED_ERROR_TYPE);
    }
}

/**
 * OAuth2.0 Netatmo client-side implementation.
 *
 * @author Originally written by Thomas Rosenblatt <thomas.rosenblatt@netatmo.com>.
 */
class NAApiClient
{
    /**
   * Array of persistent variables stored.
   */
    protected $conf = array();
    protected $refresh_token;
    protected $access_token;
    /**
   * Returns a persistent variable.
   *
   * To avoid problems, always use lower case for persistent variable names.
   *
   * @param $name
   *   The name of the variable to return.
   * @param $default
   *   The default value to use if this variable has never been set.
   *
   * @return
   *   The value of the variable.
   */
    public function getVariable($name, $default = NULL)
    {
        return isset($this->conf[$name]) ? $this->conf[$name] : $default;
    }
    /**
    * Returns the current refresh token
    */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    /**
    * Sets a persistent variable.
    *
    * To avoid problems, always use lower case for persistent variable names.
    *
    * @param $name
    *   The name of the variable to set.
    * @param $value
    *   The value to set.
    */
    public function setVariable($name, $value)
    {
        $this->conf[$name] = $value;
        return $this;
    }

    private function updateSession()
    {
        $cb = $this->getVariable("func_cb");
        $object = $this->getVariable("object_cb");
        if($object && $cb)
        {
            if(method_exists($object, $cb))
            {
                call_user_func_array(array($object, $cb), array(array("access_token"=> $this->access_token, "refresh_token" => $this->refresh_token)));
            }
        }
        else if($cb && is_callable($cb))
        {
            call_user_func_array($cb, array(array("access_token" => $this->access_token, "refresh_token" => $this->refresh_token)));
        }
    }

    private function setTokens($value)
    {
        if(isset($value["access_token"]))
        {
            $this->access_token = $value["access_token"];
            $update = true;
        }
        if(isset($value["refresh_token"]))
        {
            $this->refresh_token = $value["refresh_token"];
            $update = true;
        }
        if(isset($update)) $this->updateSession();
    }

    /**
     * Set token stored by application (in session generally) into this object
    **/
    public function setTokensFromStore($value)
    {
         if(isset($value["access_token"]))
            $this->access_token = $value["access_token"];
        if(isset($value["refresh_token"]))
            $this->refresh_token = $value["refresh_token"];
    }
    public function unsetTokens()
    {
        $this->access_token = null;
        $this->refresh_token = null;
    }
    /**
    * Initialize a NA OAuth2.0 Client.
    *
    * @param $config
    *   An associative array as below:
    *   - code: (optional) The authorization code.
    *   - username: (optional) The username.
    *   - password: (optional) The password.
    *   - client_id: (optional) The application ID.
    *   - client_secret: (optional) The application secret.
    *   - refresh_token: (optional) A stored refresh_token to use
    *   - access_token: (optional) A stored access_token to use
    *   - object_cb : (optionale) An object for which func_cb method will be applied if object_cb exists
    *   - func_cb : (optional) A method called back to store tokens in its context (session for instance)
    */
    public function __construct($config = array())
    {
        // If tokens are provided let's store it
        if(isset($config["access_token"]))
        {
            $this->access_token = $config["access_token"];
            unset($access_token);
        }
        if(isset($config["refresh_token"]))
        {
            $this->refresh_token = $config["refresh_token"];
        }
        // We must set uri first.
        $uri = array("base_uri" => BACKEND_BASE_URI, "services_uri" => BACKEND_SERVICES_URI, "access_token_uri" => BACKEND_ACCESS_TOKEN_URI, "authorize_uri" => BACKEND_AUTHORIZE_URI);
        foreach($uri as $key => $val)
        {
            if(isset($config[$key]))
            {
                $this->setVariable($key, $config[$key]);
                unset($config[$key]);
            }
            else
            {
                $this->setVariable($key, $val);
            }
        }

        // Other else configurations.
        foreach ($config as $name => $value)
        {
            $this->setVariable($name, $value);
        }

        if($this->getVariable("code") == null && isset($_GET["code"]))
        {
            $this->setVariable("code", $_GET["code"]);
        }

        if(isset($config["scope"]))
        {
            $this->scope = $config['scope'];
        }
  }

    /**
   * Default options for cURL.
   */
    public static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HEADER         => TRUE,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'netatmoclient',
        CURLOPT_SSL_VERIFYPEER => TRUE,
        CURLOPT_HTTPHEADER     => array("Accept: application/json"),
    );

    /**
    * Makes an HTTP request.
    *
    * This method can be overriden by subclasses if developers want to do
    * fancier things or use something other than cURL to make the request.
    *
    * @param $path
    *   The target path, relative to base_path/service_uri or an absolute URI.
    * @param $method
    *   (optional) The HTTP method (default 'GET').
    * @param $params
    *   (optional The GET/POST parameters.
    * @param $ch
    *   (optional) An initialized curl handle
    *
    * @return
    *   The json_decoded result or NAClientException if pb happend
    */
    public function makeRequest($path, $method = 'GET', $params = array())
    {
        $ch = curl_init();
        $opts = self::$CURL_OPTS;
        if ($params)
        {
            switch ($method)
            {
                case 'GET':
                    $path .= '?' . http_build_query($params, NULL, '&');
                break;
                // Method override as we always do a POST.
                default:
                    if ($this->getVariable('file_upload_support'))
                    {
                        $opts[CURLOPT_POSTFIELDS] = $params;
                    }
                    else
                    {
                        $opts[CURLOPT_POSTFIELDS] = http_build_query($params, NULL, '&');
                    }
                break;
            }
        }
        $opts[CURLOPT_URL] = $path;
        // Disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
        // for 2 seconds if the server does not support this header.
        if (isset($opts[CURLOPT_HTTPHEADER]))
        {
            $existing_headers = $opts[CURLOPT_HTTPHEADER];
            $existing_headers[] = 'Expect:';
            $ip = $this->getVariable("ip");
            if($ip)
                $existing_headers[] = 'CLIENT_IP: '.$ip;
            $opts[CURLOPT_HTTPHEADER] = $existing_headers;
        }
        else
        {
            $opts[CURLOPT_HTTPHEADER] = array('Expect:');
        }
        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);


        $errno = curl_errno($ch);
        // CURLE_SSL_CACERT || CURLE_SSL_CACERT_BADFILE
        if ($errno == 60 || $errno == 77)
        {
            echo "WARNING ! SSL_VERIFICATION has been disabled since ssl error retrieved. Please check your certificate http://curl.haxx.se/docs/sslcerts.html\n";
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $result = curl_exec($ch);
        }

        if ($result === FALSE)
        {
            $e = new NACurlErrorType(curl_errno($ch), curl_error($ch));
            curl_close($ch);
            throw $e;
        }
        curl_close($ch);

        // Split the HTTP response into header and body.
        list($headers, $body) = explode("\r\n\r\n", $result);
        $headers = explode("\r\n", $headers);
        //Only 2XX response are considered as a success
        if(strpos($headers[0], 'HTTP/1.1 2') !== FALSE)
        {
            $decode = json_decode($body, TRUE);
            if(!$decode)
            {
                if (preg_match('/^HTTP\/1.1 ([0-9]{3,3}) (.*)$/', $headers[0], $matches))
                {
                    throw new NAJsonErrorType($matches[1], $matches[2]);
                }
                else throw new NAJsonErrorType(200, "OK");
            }
            return $decode;
        }
        else
        {
            if (!preg_match('/^HTTP\/1.1 ([0-9]{3,3}) (.*)$/', $headers[0], $matches))
            {
                $matches = array("", 400, "bad request");
            }
            $decode = json_decode($body, TRUE);
            if(!$decode)
            {
                throw new NAApiErrorType($matches[1], $matches[2], null);
            }
            throw new NAApiErrorType($matches[1], $matches[2], $decode);
        }
    }

    /**
    * Retrieve an access token following the best grant to recover it (order id : code, refresh_token, password)
    *
    * @return
    * A valid array containing at least an access_token as an index
    *  @throw
    * A NAClientException if unable to retrieve an access_token
    */
    public function getAccessToken()
    {
        //find best way to retrieve access_token
        if($this->access_token) return array("access_token" => $this->access_token);
        if($this->getVariable('code'))// grant_type == authorization_code.
        {
            return $this->getAccessTokenFromAuthorizationCode($this->getVariable('code'));
        }
        else if($this->refresh_token)// grant_type == refresh_token
        {
            return $this->getAccessTokenFromRefreshToken($this->refresh_token);
        }
        else if($this->getVariable('username') && $this->getVariable('password'))  //grant_type == password
        {
            return $this->getAccessTokenFromPassword($this->getVariable('username'), $this->getVariable('password'));
        }
        else throw new NAInternalErrorType("No access token stored");
    }


    /**
    * Get url to redirect to oauth2.0 netatmo authorize endpoint
    * This is the url where app server needing netatmo access need to route their user (via redirect)
    *
    *
    * @param scope
    *   scope used here
    * @param state
    *   state returned in redirect_uri
    */
    public function getAuthorizeUrl($scope = null, $state = null)
    {
        $redirect_uri = $this->getRedirectUri();
        if($state == null)
        {
            $state = rand();
        }
        $params = array("scope" => $scope, "state" => $state, "client_id" => $this->getVariable("client_id"), "client_secret" => $this->getVariable("client_secret"), "response_type" => "code", "redirect_uri" => $redirect_uri);
        return $this->getUri($this->getVariable("authorize_uri"), $params);
    }

    /**
    * Get access token from OAuth2.0 token endpoint with authorization code.
    *
    * This function will only be activated if both access token URI, client
    * identifier and client secret are setup correctly.
    *
    * @param $code
    *   Authorization code issued by authorization server's authorization
    *   endpoint.
    *
    * @return
    *   A valid OAuth2.0 JSON decoded access token in associative array
    * @thrown
    *  A NAClientException if unable to retrieve an access_token
    */
    private function getAccessTokenFromAuthorizationCode($code)
    {
        $redirect_uri = $this->getRedirectUri();
        $scope = $this->getVariable('scope');
        if($scope == null)
        {
            $scope = "rs";
        }
        if($this->getVariable('access_token_uri') && ($client_id = $this->getVariable('client_id')) != NULL && ($client_secret = $this->getVariable('client_secret')) != NULL && $redirect_uri != NULL)
        {
            $ret = $this->makeRequest($this->getVariable('access_token_uri'),
                'POST',
                array(
                    'grant_type' => 'authorization_code',
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'code' => $code,
                    'redirect_uri' => $redirect_uri,
                )
            );
            $this->setTokens($ret);
            return $ret;
        }
        else
            throw new NAInternalErrorType("missing args for getting authorization code grant");
    }

  /**
   * Get access token from OAuth2.0 token endpoint with basic user
   * credentials.
   *
   * This function will only be activated if both username and password
   * are setup correctly.
   *
   * @param $username
   *   Username to be check with.
   * @param $password
   *   Password to be check with.
   *
    * @return
    *   A valid OAuth2.0 JSON decoded access token in associative array
    * @thrown
    *  A NAClientException if unable to retrieve an access_token
   */
    private function getAccessTokenFromPassword($username, $password)
    {
        $scope = $this->getVariable('scope');
        if($scope == null)
        {
            $scope = "rs";
        }
        if ($this->getVariable('access_token_uri') && ($client_id = $this->getVariable('client_id')) != NULL && ($client_secret = $this->getVariable('client_secret')) != NULL)
        {
            $ret = $this->makeRequest(
                $this->getVariable('access_token_uri'),
                'POST',
                array(
                'grant_type' => 'password',
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'username' => $username,
                'password' => $password,
                'scope' => $scope,
                )
            );
            $this->setTokens($ret);
            return $ret;
        }
        else
            throw new NAInternalErrorType("missing args for getting password grant");
    }

    /**
    * Get access token from OAuth2.0 token endpoint with basic user
    * credentials.
    *
    * This function will only be activated if both username and password
    * are setup correctly.
    *
    * @param $username
    *   Username to be check with.
    * @param $password
    *   Password to be check with.
    *
    * @return
    *   A valid OAuth2.0 JSON decoded access token in associative array
    * @thrown
    *  A NAClientException if unable to retrieve an access_token
    */
    private function getAccessTokenFromRefreshToken()
    {
        if ($this->getVariable('access_token_uri') && ($client_id = $this->getVariable('client_id')) != NULL && ($client_secret = $this->getVariable('client_secret')) != NULL && ($refresh_token = $this->refresh_token) != NULL)
        {
            $ret = $this->makeRequest(
                $this->getVariable('access_token_uri'),
                'POST',
                array(
                    'grant_type' => 'refresh_token',
                    'client_id' => $this->getVariable('client_id'),
                    'client_secret' => $this->getVariable('client_secret'),
                    'refresh_token' => $refresh_token,
                )
            );
            $this->setTokens($ret);
            return $ret;
        }
        else
            throw new NAInternalErrorType("missing args for getting refresh token grant");
    }

    /**
    * Make an OAuth2.0 Request.
    *
    * Automatically append "access_token" in query parameters
    *
    * @param $path
    *   The target path, relative to base_path/service_uri
    * @param $method
    *   (optional) The HTTP method (default 'GET').
    * @param $params
    *   (optional The GET/POST parameters.
    *
    * @return
    *   The JSON decoded response object.
    *
    * @throws OAuth2Exception
    */
    protected function makeOAuth2Request($path, $method = 'GET', $params = array(), $reget_token = true)
    {
        try
        {
            $res = $this->getAccessToken();
        }
        catch(NAApiErrorType $ex)
        {
            throw new NANotLoggedErrorType($ex->getCode(), $ex->getMessage());
        }
        $params["access_token"] = $res["access_token"];
        try
        {
            $res = $this->makeRequest($path, $method, $params);
            return $res;
        }
        catch(NAApiErrorType $ex)
        {
            if($reget_token == true)
            {
                switch($ex->getCode())
                {
                    case NARestErrorCode::INVALID_ACCESS_TOKEN:
                    case NARestErrorCode::ACCESS_TOKEN_EXPIRED:
                        //Ok token has expired let's retry once
                        if($this->refresh_token)
                        {
                            try
                            {
                                $this->getAccessTokenFromRefreshToken();//exception will be thrown otherwise
                }
                catch(Exception $ex2)
                            {
                                //Invalid refresh token TODO: Throw a special exception
                                throw $ex;
                            }
                        }
                        else throw $ex;

                        return $this->makeOAuth2Request($path, $method, $params, false);
                    break;
                    default:
                        throw $ex;
                }
            }
            else throw $ex;
        }
        return $res;
    }
    /**
     * Make an API call.
     *
     * Support both OAuth2.0 or normal GET/POST API call, with relative
     * or absolute URI.
     *
     * If no valid OAuth2.0 access token found in session object, this function
     * will automatically switch as normal remote API call without "access_token"
     * parameter.
     *
     * Assume server reply in JSON object and always decode during return. If
     * you hope to issue a raw query, please use makeRequest().
     *
     * @param $path
     *   The target path, relative to base_path/service_uri or an absolute URI.
     * @param $method
     *   (optional) The HTTP method (default 'GET').
     * @param $params
     *   (optional The GET/POST parameters.
     *
     * @return
     *   The JSON decoded body response object.
     *
     * @throws NAClientException
    */
    public function api($path, $method = 'GET', $params = array(), $secure = false)
    {
        if (is_array($method) && empty($params))
        {
            $params = $method;
            $method = 'GET';
        }

        // json_encode all params values that are not strings.
        foreach ($params as $key => $value)
        {
            if (!is_string($value))
            {
                $params[$key] = json_encode($value);
            }
        }
    $res = $this->makeOAuth2Request($this->getUri($path, array(), $secure), $method, $params);
        if(isset($res["body"])) return $res["body"];
    else return $res;
    }

    /**
     * Make a REST call to a Netatmo server that do not need access_token
     *
     * @param $path
     *   The target path, relative to base_path/service_uri or an absolute URI.
     * @param $method
     *   (optional) The HTTP method (default 'GET').
     * @param $params
     *   (optional The GET/POST parameters.
     *
     * @return
     *   The JSON decoded response object.
     *
     * @throws NAClientException
    */
    public function noTokenApi($path, $method = 'GET', $params = array())
    {
        if (is_array($method) && empty($params))
        {
            $params = $method;
            $method = 'GET';
        }

        // json_encode all params values that are not strings.
        foreach ($params as $key => $value)
        {
            if (!is_string($value))
            {
                $params[$key] = json_encode($value);
            }
        }

        return $this->makeRequest($path, $method, $params);
    }

    static public function str_replace_once($str_pattern, $str_replacement, $string)
    {
        if (strpos($string, $str_pattern) !== false)
        {
            $occurrence = strpos($string, $str_pattern);
            return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
        }
        return $string;
    }

    /**
    * Since $_SERVER['REQUEST_URI'] is only available on Apache, we
    * generate an equivalent using other environment variables.
    */
    function getRequestUri()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        else {
            if (isset($_SERVER['argv'])) {
                $uri = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['argv'][0];
            }
            elseif (isset($_SERVER['QUERY_STRING'])) {
                $uri = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
            }
            else {
                $uri = $_SERVER['SCRIPT_NAME'];
            }
        }
        // Prevent multiple slashes to avoid cross site requests via the Form API.
        $uri = '/' . ltrim($uri, '/');

        return $uri;
    }

  /**
   * Returns the Current URL.
   *
   * @return
   *   The current URL.
   */
    protected function getCurrentUri()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'
          ? 'https://'
          : 'http://';
        $current_uri = $protocol . $_SERVER['HTTP_HOST'] . $this->getRequestUri();
        $parts = parse_url($current_uri);

        $query = '';
        if (!empty($parts['query'])) {
          $params = array();
          parse_str($parts['query'], $params);
          $params = array_filter($params);
          if (!empty($params)) {
            $query = '?' . http_build_query($params, NULL, '&');
          }
        }

        // Use port if non default.
        $port = isset($parts['port']) &&
          (($protocol === 'http://' && $parts['port'] !== 80) || ($protocol === 'https://' && $parts['port'] !== 443))
          ? ':' . $parts['port'] : '';

        // Rebuild.
        return $protocol . $parts['host'] . $port . $parts['path'] . $query;
    }

    /**
     * Returns the Current URL.
     *
     * @return
     *   The current URL.
    */
    protected function getRedirectUri()
    {
        $redirect_uri = $this->getVariable("redirect_uri");
        if(!empty($redirect_uri)) return $redirect_uri;
        else return $this->getCurrentUri();
    }

    /**
    * Build the URL for given path and parameters.
    *
    * @param $path
    *   (optional) The path.
    * @param $params
    *   (optional) The query parameters in associative array.
    *
    * @return
    *   The URL for the given parameters.
    */
    protected function getUri($path = '', $params = array(), $secure = false)
    {
        $url = $this->getVariable('services_uri') ? $this->getVariable('services_uri') : $this->getVariable('base_uri');
        if($secure == true)
        {
            $url = self::str_replace_once("http", "https", $url);
        }
        if(!empty($path))
            if (substr($path, 0, 4) == "http")
                $url = $path;
            else if(substr($path, 0, 5) == "https")
                $url = $path;
            else
                $url = rtrim($url, '/') . '/' . ltrim($path, '/');

        if (!empty($params))
            $url .= '?' . http_build_query($params, NULL, '&');

        return $url;
    }
}
/**
 * API Helpers
 *
 * @author Originally written by Fred Potter <fred.potter@netatmo.com>.
 */
class NAApiHelper
{
    public function simplifyDeviceList($devicelist)
    {
        foreach ($devicelist["devices"] as $d=>$device)
        {
            $moduledetails=Array();
            foreach ($device["modules"] as $module)
            {
                foreach ($devicelist["modules"] as $moduledetail)
                {
                    if ($module==$moduledetail['_id'])
                    {
                        $moduledetails[]=$moduledetail;
                    }
                }
            }
            unset($devicelist["devices"][$d]["modules"]);
            $devicelist["devices"][$d]["modules"]=$moduledetails;
        }
        unset($devicelist["modules"]);
        return($devicelist);
    }
    public function getLastMeasure($client, $device, $module=null)
    {
        $params = array("scale" => "max", "type" => "Temperature,CO2,Humidity,Pressure,Noise", "date_end" => "last", "device_id" => $device);
        $result = array();
        if (!is_null($module))
        {
            $params["module_id"]=$module;
        }
        $meas = $client->api("getmeasure", "POST", $params);
        if(isset($meas[0]))
        {
            $result['time'] = $meas[0]['beg_time'];
            if ($meas[0]['value'][0][0]!='') $result['Temperature']=$meas[0]['value'][0][0];
            if ($meas[0]['value'][0][1]!='') $result['CO2']=        $meas[0]['value'][0][1];
            if ($meas[0]['value'][0][2]!='') $result['Humidity']=   $meas[0]['value'][0][2];
            if ($meas[0]['value'][0][3]!='') $result['Pressure']=   $meas[0]['value'][0][3];
            if ($meas[0]['value'][0][4]!='') $result['Noise']=      $meas[0]['value'][0][4];
        }
        return($result);

    }
    public function getLastMeasures($client,$simplifieddevicelist)
    {
        $results = Array();
        foreach ($simplifieddevicelist["devices"] as $device)
        {
            $result = Array();
            if(isset($device["station_name"])) $result["station_name"] = $device["station_name"];
            if(isset($device["modules"][0])) $result["modules"][0]["module_name"] = $device["module_name"];
            $result["modules"][0] = array_merge($result["modules"][0], $this->GetLastMeasure($client,$device["_id"]));
            foreach ($device["modules"] as $module)
            {
                $addmodule = Array();
                if(isset($module["module_name"])) $addmodule["module_name"] = $module["module_name"];
                $addmodule = array_merge($addmodule, $this->getLastMeasure($client,$device["_id"],$module["_id"]));
                $result["modules"][] = $addmodule;
            }
            $results[] = $result;
        }
        return($results);
    }
}

?>
