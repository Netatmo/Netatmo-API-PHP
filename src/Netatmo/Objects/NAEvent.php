<?php

namespace Netatmo\Objects;

use Netatmo\Exceptions\NASDKException;
use Netatmo\Common\NACameraEventType;
use Netatmo\Common\NACameraEventInfo;
use Netatmo\Common\NASDKErrorCode;

/**
* Class NAEvent
*/
class NAEvent extends NAObjectWithPicture
{
    private static $videoEvents = array(NACameraEventType::CET_PERSON, NACameraEventType::CET_MOVEMENT);

    /**
    *
    * @brief returns event's snapshot
    */
    public function getSnapshot()
    {
        $snapshot = $this->getVar(NACameraEventInfo::CEI_SNAPSHOT);
        return $this->getPictureURL($snapshot);
    }

    /**
    * @return string
    * @brief returns event's description
    */
    public function getMessage()
    {
        return $this->getVar(NACameraEventInfo::CEI_MESSAGE);
    }

    /**
    * @return timestamp
    * @brief returns at which time the event has been triggered
    */
    public function getTime()
    {
        return $this->getVar(NACameraEventInfo::CEI_TIME);
    }

    /**
    * @return string
    * @brief returns the event's type
    */
    public function getEventType()
    {
        return $this->getVar(NACameraEventInfo::CEI_TYPE);
    }

    /**
    * @return int
    * @brief returns event's subtype for SD Card & power adapter events
    * @throw NASDKException
    */
    public function getEventSubType()
    {
        if($this->getEventType() === NACameraEventType::CET_SD
            || $this->getEventType() === NACameraEventType::CET_ALIM)
        {
            return $this->getVar(NACameraEventInfo::CEI_SUB_TYPE);
        }
        else throw new NASDKException(NASDKErrorCode::INVALID_FIELD, "This field does not exist for this type of event");
    }

    /**
    * @return string
    * @brief returns id of the camera that triggered the event
    */
    public function getCameraId()
    {
        return $this->getVar(NACameraEventInfo::CEI_CAMERA_ID);
    }

    /**
    * @return string
    * @brief returns id of the person seen in the event
    * @throw NASDKException
    */
    public function getPersonId()
    {
        if($this->getEventType() === NACameraEventType::CET_PERSON
            || $this->getEventType() === NACameraEventType::CET_PERSON_AWAY
        )
        {
            return $this->getVar(NACameraEventInfo::CEI_PERSON_ID);
        }
        else throw new NASDKException(NASDKErrorCode::INVALID_FIELD, "This field does not exist for this type of event");

    }

    public function hasVideo()
    {
        if(in_array($this->getEventType(), $this->videoEvents))
            return TRUE;
        else return FALSE;
    }

    /**
    * @return string
    * @brief returns event's video id
    * @throw NASDKException
    */
    public function getVideo()
    {
        if($this->hasVideo())
            return $this->getVar(NACameraEventInfo::CEI_VIDEO_ID);
        else throw new NASDKException(NASDKErrorCode::INVALID_FIELD, "This type of event does not have videos");
    }

    /**
    * @return string
    * @brief returns event's video status
    * @throw NASDKException
    */
    public function getVideoStatus()
    {
        if($this->hasVideo())
            return $this->getVar(NACameraEventInfo::CEI_VIDEO_STATUS);
        else throw new NASDKException(NASDKErrorCode::INVALID_FIELD, "This type of event does not have videos");

    }

    /**
    * @return boolean
    * @brief returns whether or not this event corresponds to the moment where the person arrived home
    * @throw NASDKException
    */
    public function isPersonArrival()
    {
        if($this->getEventType() === NACameraEventType::CET_PERSON)
        {
            return $this->getVar(NACameraEventInfo::CEI_IS_ARRIVAL);
        }
        else throw new NASDKException(NASDKErrorCode::INVALID_FIELD, "This field does not exist for this type of event");

    }
}
?>
