<?php

namespace Netatmo\Clients;

use Netatmo\Handlers\NAResponseHandler;
use Netatmo\Exceptions\NAClientException;
use Netatmo\Common\NARestErrorCode;

/**
 * NETATMO Welcome API PHP CLIENT
 *
 * For more details upon NETATMO API, please check https://dev.netatmo.com/doc
 * @author Originally written by Enzo Macri <enzo.macri@netatmo.com>
 */
class NAWelcomeApiClient extends NAApiClient
{

    /*
    * @type PRIVATE API
    * @param string $home_id
    * @param integer size (optional): number of events requested per home
    * @return NAResponseHandler
    * @brief Method use to retrieve data for the given home, or all the home belonging to the user
    */
    public function getData($home_id = NULL, $size = 30)
    {
        $params = array('size' => $size);
        if(!is_null($home_id)) $params['home_id'] = $home_id;

        return new NAResponseHandler($this->api('gethomedata', $params));
    }

    /*
    * @type PRIVATE API
    * @param string $home_id
    * @param string $person_id
    * @param integer $offset (optional): number of events you want to retrieve further than the last event of the given person. Default 0.
    * @return NAResponseHandler
    * @brief Method used to retrieve every events until the last one of the given person (plus eventually the given offset)
    */
    public function getLastEventOf($home_id, $person_id, $offset = 0)
    {
        $params = array("home_id" => $home_id,
                        "person_id" => $person_id,
                        "offset" => $offset);

        return new NAResponseHandler($this->api("getlasteventof", $params));
    }

    /*
    * @type PRIVATE API
    * @param string $home_id
    * @param string $event_id
    * @return NAResponseHandler
    * @brief Method used to retrieve every events until the given one
    */
    public function getEventsUntil($home_id, $event_id)
    {
        $params = array("home_id" => $home_id,
                        "event_id" => $event_id);

        return new NAResponseHandler($this->api("geteventsuntil", $params));
    }

    /*
    * @type PRIVATE API
    * @param string $home_id
    * @param string $event_id
    * @param size (optional) Number of events you want to retrieve. Default 30
    * @return NAResponseHandler
    * @brief Method used to retrieve events older than the given one
    */
    public function getNextEvents($home_id, $event_id, $size = 30)
    {
        $params = array("home_id" => $home_id,
                        "event_id" => $event_id,
                        "size" => $size);

        return new NAResponseHandler($this->api("getnextevents", $params));
    }

    /*
    * @type PRIVATE API
    * @param array $picture: contains picture information such as id, version & key. Correspond to events snapshot or persons face
    * @return picture's URL
    * @brief Method used to retrieve an event snapshot or a person face
    */
    public function getCameraPicture($picture)
    {

        if(isset($picture['id']) && isset($picture['key']))
        {
            $src = $this->getVariable('services_uri');
            $src.= "/getcamerapicture?image_id=".$picture['id']."&key=".$picture['key'];
            return $src;
        }
        else throw new NAApiErrorType(NARestErrorCode::MISSING_ARGS, "Missing args", NULL);

    }

    /**
    * @param string $url : webhook url
    * @brief register your app to webhook notification for the current user
    */
    public function subscribeToWebhook($url)
    {
        $this->addWebhook($url, "app_camera");
    }

    /**
    * @brief drop webhook notifications for the current user
    */
    public function dropWebhook()
    {
        parent::dropWebhook("app_camera");
    }
}

?>
