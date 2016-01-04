<?php

namespace Netatmo\Common;

class NABatteryLevelWindGaugeModule
{
    /* Battery range: 6000 ... 3950 */
    const WG_BATTERY_LEVEL_0 = 5590;/*full*/
    const WG_BATTERY_LEVEL_1 = 5180;/*high*/
    const WG_BATTERY_LEVEL_2 = 4770;/*medium*/
    const WG_BATTERY_LEVEL_3 = 4360;/*low*/
    /* below 4360: very low */
}

?>
