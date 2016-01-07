<?php

namespace Netatmo\Common;

class NABatteryLevelModule
{
    /* Battery range: 6000 ... 3600 */
    const BATTERY_LEVEL_0 = 5500;/*full*/
    const BATTERY_LEVEL_1 = 5000;/*high*/
    const BATTERY_LEVEL_2 = 4500;/*medium*/
    const BATTERY_LEVEL_3 = 4000;/*low*/
    /* below 4000: very low */
}

?>
