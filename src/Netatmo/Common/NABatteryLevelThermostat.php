<?php

namespace Netatmo\Common;

class NABatteryLevelThermostat
{
    /* Battery range: 4500 ... 3000 */
    const THERMOSTAT_BATTERY_LEVEL_0 = 4100;/*full*/
    const THERMOSTAT_BATTERY_LEVEL_1 = 3600;/*high*/
    const THERMOSTAT_BATTERY_LEVEL_2 = 3300;/*medium*/
    const THERMOSTAT_BATTERY_LEVEL_3 = 3000;/*low*/
    /* below 3000: very low */
}

?>
