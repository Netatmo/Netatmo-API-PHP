<?php

namespace Netatmo\Handlers;

use Netatmo\Objects\NAHome;
use Netatmo\Objects\NACamera;
use Netatmo\Objects\NAPerson;
use Netatmo\Objects\NAEvent;
use Netatmo\Exceptions\NASDKExceptions;
use Netatmo\Common\NASDKErrorCode;


/**
*
* Netatmo Welcome Response Handler
* class handling Api client response : enables to get either Raw Data or Instantiated Objects
*/
class NAResponseHandler
{
    private $decodedBody;
    private $dataCollection;

    public function __construct($responseBody)
    {
        $this->decodedBody = $responseBody;
    }

    /**
    * @return array $decodedBody
    * @brief return raw data retrieved from Netatmo API
    */
    public function getDecodedBody()
    {
        return $this->decodedBody;
    }

    /**
    * return array $dataCollection : array of home or event objects
    * @brief return data as collection objects
    * @throw NASDKException
    */
    public function getData()
    {
        if(!is_null($this->decodedBody) && !empty($this->decodedBody))
        {
            if(is_null($this->dataCollection) || empty($this->dataCollection))
            {
                if(isset($this->decodedBody['homes']))
                {
                    $this->buildHomeCollectionFromResponse();
                }
                else if (isset($this->decodedBody['events_list']))
                {
                    $this->buildEventCollectionFromResponse();
                }
            }

            return $this->dataCollection;

        }

        else throw new NASDKException(NASDKErrorCode::UNABLE_TO_CAST, "Empty Response.");

    }

    /**
    * @brief convert raw data to home objects
    * @throw NASDKException
    */
    public function buildHomeCollectionFromResponse()
    {
        $this->validateCastToHomeCollection();

        $homeCollection = array();

        foreach($this->decodedBody['homes'] as $homeArray)
        {
            $this->validateCastToHome($homeArray);
            $home = new NAHome($homeArray);
            $homeCollection[] = $home;
        }

        $this->dataCollection = $homeCollection;
    }

    /**
    * @brief check if array of data is castable to collection of home objects
    * @throw NASDKException
    */
    protected function validateCastToHomeCollection()
    {
        if(is_array($this->decodedBody) && $this->validateArrayForCast($this->decodedBody, 'homes'))
            return;

        throw new NASDKException(NASDKErrorCode::UNABLE_TO_CAST, "Unable to cast data to NAHome object");
    }

    /**
    * @brief check if array of data is castable to NAHome object
    * @throw NASDKException
    */
    protected function validateCastToHome($data)
    {
        if(isset($data['id'])
            && $this->validateArrayForCast($data, 'persons')
            && $this->validateArrayForCast($data, 'events')
            && $this->validateArrayForCast($data, 'cameras')
        )
            return;

        throw new NASDKException(NASDKErrorCode::UNABLE_TO_CAST, "Unable to cast data to home object");
    }

    /**
    * @brief check if array is castable
    */
    private function validateArrayForCast($array, $name)
    {
        if(isset($array[$name]) && is_array($array[$name]))
            return true;
        else return false;
    }

    /**
    * @brief convert raw data to collection of event objects
    * @throw NASDKException
    */
    public function buildEventCollectionFromResponse()
    {
        $this->validateCastToEventCollection();

        $eventCollection = array();

        foreach($this->decodedBody['events_list'] as $eventArray)
        {
            $this->validateCastToEventObject($eventArray);
            $event = new NAEvent($eventArray);
            $eventCollection[] = $event;
        }

        $this->dataCollection = $eventCollection;
    }

    /**
    * @brief check if array of data is castable to collection of event objects
    * @throw NASDKException
    */
    protected function validateCastToEventCollection()
    {
        if(is_array($this->decodedBody)
            && $this->validateArrayForCast($this->decodedBody, 'events_list')
        )
            return;

        throw new NASDKException(NASDKErrorCode::UNABLE_TO_CAST, "Impossible to cast to NAEvent");
    }

    /**
    * @brief check if array of data is castable to NAEvent object
    * @throw NASDKException
    */
    protected function validateCastToEventObject($data)
    {
        if(isset($data['id']) && isset($data['time']) && isset($data['type']))
            return;

        throw new NASDKException(NASDKErrorCode::UNABLE_TO_CAST, "Unable to cast data to event object");
    }
}
?>
