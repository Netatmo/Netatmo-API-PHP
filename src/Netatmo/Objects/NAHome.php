<?php

namespace Netatmo\Objects;

use Netatmo\Common\NACameraHomeInfo;
use Netatmo\Exceptions\NASDKException;
use Netatmo\Common\NASDKErrorCode;

/**
* Class NAHome
*
*/
class NAHome extends NAObject
{

    public function __construct($array)
    {
        parent::__construct($array);

        if(isset($array[NACameraHomeInfo::CHI_PERSONS]))
        {
            $personArray = array();
            foreach($array[NACameraHomeInfo::CHI_PERSONS] as $person)
            {
                $personArray[] = new NAPerson($person);
            }
            $this->object[NACameraHomeInfo::CHI_PERSONS] = $personArray;
        }

        if(isset($array[NACameraHomeInfo::CHI_EVENTS]))
        {
            $eventArray = array();
            foreach($array[NACameraHomeInfo::CHI_EVENTS] as $event)
            {
                $eventArray[] = new NAEvent($event);
            }
            $this->object[NACameraHomeInfo::CHI_EVENTS] = $eventArray;
        }

        if(isset($array[NACameraHomeInfo::CHI_CAMERAS]))
        {
            $cameraArray = array();
            foreach($array[NACameraHomeInfo::CHI_CAMERAS] as $camera)
            {
                $cameraArray[] = new NACamera($camera);
            }
            $this->object[NACameraHomeInfo::CHI_CAMERAS] = $cameraArray;
        }
    }

    /**
    * @return string
    * @brief returns home's name
    */
    public function getName()
    {
        return $this->getVar(NACameraHomeInfo::CHI_NAME);
    }

    /**
    * @return array of event objects
    * @brief returns home timeline of event
    */
    public function getEvents()
    {
        return $this->getVar(NACameraHomeInfo::CHI_EVENTS, array());
    }

    /**
    * @return array of person objects
    * @brief returns every person belonging to this home
    */
    public function getPersons()
    {
        return $this->getVar(NACameraHomeInfo::CHI_PERSONS, array());
    }

    /**
    * @return array of person objects
    * @brief returns every known person belonging to this home
    */
    public function getKnownPersons()
    {
        $knowns = array();
        foreach($this->getVar(NACameraHomeInfo::CHI_PERSONS, array()) as $person)
        {
            if($person->isKnown())
                $knowns[] = $person;
        }
        return $knowns;
    }

    /**
    * @return array of person objects
    * @brief returns every unknown person belonging to this home
    */
    public function getUnknownPersons()
    {
        $unknowns = array();
        foreach($this->getVar(NACameraHomeInfo::CHI_PERSONS, array()) as $person)
        {
            if($person->isUnknown())
                $unknowns[] = $person;
        }
        return $unknowns;
    }

    /**
    * @return array of camera objects
    * @brief returns every camera belonging to this home
    */
    public function getCameras()
    {
        return $this->getVar(NACameraHomeInfo::CHI_CAMERAS, array());
    }

    /**
    * @return string
    * @brief returns home's timezone
    */
    public function getTimezone()
    {
        $place = $this->getVar(NACameraHomeInfo::CHI_PLACE);
        return isset($place['timezone'])? $place['timezone'] : 'GMT';
    }

    /**
    * @return NACamera
    * @brief return the camera object corresponding to the id asked
    * @throw NASDKErrorException
    */
    public function getCamera($camera_id)
    {
        foreach($this->getVar(NACameraHomeInfo::CHI_CAMERAS, array()) as $camera)
        {
            if($camera->getId() === $camera_id)
            {
                return $camera;
            }
        }
        throw new NASDKException(NASDKErrorCode::NOT_FOUND, "camera $camera_id not found in home: " . $this->getId());
    }

    /**
    * @return NAPerson
    * @brief returns NAPerson object corresponding to the id in parameter
    * @throw NASDKErrorException
    */
    public function getPerson($person_id)
    {
        foreach($this->getVar(NACameraHomeInfo::CHI_PERSONS, array()) as $camera)
        {
            if($person->getId() === $person_id)
                return $person;
        }

        throw new NASDKException(NASDKErrorCode::NOT_FOUND, "person $person_id not found in home: " . $this->getId());
    }

    /**
    * @return array of NAPerson
    * @brief returns every person that are not home
    */
    public function getPersonAway()
    {
        $away = array();

        foreach($this->getVar(NACameraHomeInfo::CHI_PERSONS, array()) as $person)
        {
            if($person->isAway())
                $away[] = $person;
        }
        return $away;
    }

    /**
    * @return array of NAPerson
    * @brief returns every person that are home
    */
    public function getPersonAtHome()
    {
        $home = array();

        foreach($this->getVar(NACameraHomeInfo::CHI_PERSONS, array()) as $person)
        {
            if(!$person->isAway())
                $home[] = $person;
        }
        return $home;
    }
}
?>
