<?php
namespace Netatmo\Common;

class NAScopes
{
    const SCOPE_READ_STATION = "read_station";
    const SCOPE_READ_THERM = "read_thermostat";
    const SCOPE_WRITE_THERM = "write_thermostat";
    const SCOPE_READ_CAMERA = "read_camera";
    const SCOPE_WRITE_CAMERA = "write_camera";
    const SCOPE_ACCESS_CAMERA = "access_camera";
    const SCOPE_READ_JUNE = "read_june";
    const SCOPE_WRITE_JUNE = "write_june";
    const SCOPE_READ_PRESENCE = "read_presence";
    const SCOPE_WRITE_PRESENCE = "write_presence";
    const SCOPE_ACCESS_PRESENCE = "access_presence";
    static $defaultScopes = array(NAScopes::SCOPE_READ_STATION);
    static $validScopes = array(NAScopes::SCOPE_READ_STATION, NAScopes::SCOPE_READ_THERM, NAScopes::SCOPE_WRITE_THERM, NAScopes::SCOPE_READ_CAMERA, NAScopes::SCOPE_WRITE_CAMERA, NAScopes::SCOPE_ACCESS_CAMERA, NAScopes::SCOPE_READ_PRESENCE, NAScopes::SCOPE_WRITE_PRESENCE, NAScopes::SCOPE_ACCESS_PRESENCE, NAScopes::SCOPE_READ_JUNE, NAScopes::SCOPE_WRITE_JUNE);
    // scope allowed to everyone (no need to be approved)
    static $basicScopes = array(NAScopes::SCOPE_READ_STATION, NAScopes::SCOPE_READ_THERM, NASCopes::SCOPE_WRITE_THERM, NAScopes::SCOPE_READ_CAMERA, NAScopes::SCOPE_WRITE_CAMERA, NAScopes::SCOPE_READ_PRESENCE, NAScopes::SCOPE_WRITE_PRESENCE, NAScopes::SCOPE_READ_JUNE, NAScopes::SCOPE_WRITE_JUNE);
}

?>
