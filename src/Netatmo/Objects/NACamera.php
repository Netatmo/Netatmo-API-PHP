<?php

namespace Netatmo\Objects;

use Netatmo\Exceptions\NASDKException;
use Netatmo\Common\NASDKErrorCode;
use Netatmo\Common\NACameraInfo;
use Netatmo\Common\NACameraStatus;

/**
* Class NACamera
*
*/
class NACamera extends NAObject
{
    public function getGlobalStatus()
    {
        $on_off = $this->getVar(NACameraInfo::CI_STATUS);
        $sd = $this->getVar(NACameraInfo::CI_SD_STATUS);
        $power = $this->getVar(NACameraInfo::CI_ALIM_STATUS);

        if($on_off === NACameraStatus::CS_ON
            && $sd === NACameraStatus::CS_ON
            && $power === NACameraStatus::CS_ON)
        {
            return TRUE;
        }
        return FALSE;
    }

    /**
    * @return string $name
    * @brief returns the camera name
    */
    public function getName()
    {
        return $this->getVar(NACameraInfo::CI_NAME);
    }

    /**
    * @return string $vpn_url
    * @brief returns the vpn_url of the camera
    * @throw new NASDKErrorException
    */
    public function getVpnUrl()
    {
        if(!is_null($this->getVar(NACameraInfo::CI_VPN_URL)))
            return $this->getVar(NACameraInfo::CI_VPN_URL);
        else throw new NASDKException(NASDKErrorCode::FORBIDDEN_OPERATION, "You don't have access to this field due to the scope of your application");
    }

    /**
    * @return boolean $is_local
    * @brief returns whether or not the camera shares the same public address than this application
    * @throw new NASDKErrorException
    */
    public function isLocal()
    {
        if(!is_null($this->getVar(NACameraInfo::CI_IS_LOCAL)))
            return $this->getVar(NACameraInfo::CI_IS_LOCAL);
        else throw new NASDKException(NASDKErrorCode::FORBIDDEN_OPERATION, "You don't have access to this field due to the scope of your application");
    }

    public function getSDCardStatus()
    {
        return $this->getVar(NACameraInfo::CI_SD_STATUS);
    }

    public function getPowerAdapterStatus()
    {
        return $this->getVar(NACameraInfo::CI_ALIM_STATUS);
    }

    public function getMonitoringStatus()
    {
        return $this->getVar(NACameraInfo::CI_STATUS);
    }
}

?>
