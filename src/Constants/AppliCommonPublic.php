<?php
/**
* Wrapper ensuring backward compatibility with older SDK versions
*
* DEPRECATED
*
* You must not use this class
*
*/

require_once dirname(dirname(__FILE__)) . "/Netatmo/autoload.php";

class NAScopes extends Netatmo\Common\NAScopes
{
}

class NAThermZone extends Netatmo\Common\NAThermZone
{
}

class NACameraInfo extends Netatmo\Common\NACameraInfo
{
}

class NACameraHomeInfo extends Netatmo\Common\NACameraHomeInfo
{
}

class NACameraEventInfo extends Netatmo\Common\NACameraEventInfo
{
}

class NACameraEventType extends Netatmo\Common\NACameraEventType
{
}

class NACameraPersonInfo extends Netatmo\Common\NACameraPersonInfo
{
}

class NACameraImageInfo extends Netatmo\Common\NACameraImageInfo
{
}

class NACameraStatus extends Netatmo\Common\NACameraStatus
{
}

class NACameraSDEvent extends Netatmo\Common\NACameraSDEvent
{
}

class NACameraAlimSubStatus extends Netatmo\Common\NACameraAlimSubStatus
{
}

class NACameraVideoStatus extends Netatmo\Common\NACameraVideoStatus
{
}

class NAWifiRssiThreshold extends Netatmo\Common\NAWifiRssiThreshold
{
}

class NARadioRssiTreshold extends Netatmo\Common\NARadioRssiTreshold
{
}

class NAStationSensorsMinMax extends Netatmo\Common\NAStationSensorsMinMax
{
}

class NARestErrorCode extends Netatmo\Common\NARestErrorCode
{
}

class NABatteryLevelModule extends Netatmo\Common\NABatteryLevelModule
{
}

class NABatteryLevelIndoorModule extends Netatmo\Common\NABatteryLevelIndoorModule
{
}

class NABatteryLevelWindGaugeModule extends Netatmo\Common\NABatteryLevelWindGaugeModule
{
}

class NABatteryLevelThermostat extends Netatmo\Common\NABatteryLevelThermostat
{
}

?>
