<?php

namespace Netatmo\Objects;

use Netatmo\Common\NACameraPersonInfo;

/**
* class NAPerson
*/
class NAPerson extends NAObjectWithPicture
{
    /**
    * @return bool
    * @brief returns whether or not this person is known
    */
    public function isKnown()
    {
        if($this->getVar(NACameraPersonInfo::CPI_PSEUDO, FALSE))
            return TRUE;
        else return FALSE;
    }

    /**
    * @return bool
    * @brief returns whether or not this person is unknown
    */

    public function isUnknown()
    {
        return !$this->isKnown();
    }

    /**
    * @return bool
    * @brief returns whether or not this person is at home
    */
    public function isAway()
    {
        return $this->getVar(NACameraPersonInfo::CPI_OUT_OF_SIGHT);
    }

    public function getFace()
    {
        $face = $this->getVar(NACameraPersonInfo::CPI_FACE);
        return $this->getPictureURL($face);
    }

    /**
    * @return timestamp
    * @brief returns last time this person has been seen
    */
    public function getLastSeen()
    {
        return $this->getVar(NACameraPersonInfo::CPI_LAST_SEEN);
    }

    /**
    * @return string
    * @brief returns this person's name
    */
    public function getPseudo()
    {
        return $this->getVar(NACameraPersonInfo::CPI_PSEUDO);
    }
}
?>
