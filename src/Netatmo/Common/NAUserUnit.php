<?php
namespace Netatmo\Common;

class NAUserUnit
{
    const UNIT_METRIC = 0;
    const UNIT_US = 1;
    const UNIT_TYPE_NUMBER = 2;

    const UNIT_WIND_KMH = 0;
    const UNIT_WIND_MPH = 1;
    const UNIT_WIND_MS = 2;
    const UNIT_WIND_BEAUFORT = 3;
    const UNIT_WIND_KNOT = 4;
    const UNIT_WIND_NUMBER = 5;

    const UNIT_PRESSURE_MBAR = 0;
    const UNIT_PRESSURE_MERCURY = 1;
    const UNIT_PRESSURE_TORR = 2;
    const UNIT_PRESSURE_NUMBER = 3;

    const FEEL_LIKE_HUMIDEX_ALGO = 0;
    const FEEL_LIKE_HEAT_ALGO = 1;
    const FEEL_LIKE_NUMBER = 2;
}

?>
