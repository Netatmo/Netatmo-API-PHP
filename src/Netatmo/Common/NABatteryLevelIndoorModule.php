<?php
namespace Netatmo\Common;

class NABatteryLevelIndoorModule
{
    /* Battery range: 6000 ... 4200 */
    const INDOOR_BATTERY_LEVEL_0 = 5640;/*full*/
    const INDOOR_BATTERY_LEVEL_1 = 5280;/*high*/
    const INDOOR_BATTERY_LEVEL_2 = 4920;/*medium*/
    const INDOOR_BATTERY_LEVEL_3 = 4560;/*low*/
    /* Below 4560: very low */
}

?>
