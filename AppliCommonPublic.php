<?php
class NARestErrorCode
{
    const ACCESS_TOKEN_MISSING = 1;
    const INVALID_ACCESS_TOKEN = 2;
    const ACCESS_TOKEN_EXPIRED = 3;
    const INCONSISTENCY_ERROR = 4;
    const APPLICATION_DEACTIVATED = 5;
    const INVALID_EMAIL = 6;
    const NOTHING_TO_MODIFY = 7;
    const EMAIL_ALREADY_EXISTS = 8;
    const DEVICE_NOT_FOUND = 9;
    const MISSING_ARGS = 10;
    const INTERNAL_ERROR = 11;
    const DEVICE_OR_SECRET_NO_MATCH = 12;
    const OPERATION_FORBIDDEN = 13;
    const APPLICATION_NAME_ALREADY_EXISTS = 14;
    const NO_PLACES_IN_DEVICE = 15;
    const MGT_KEY_MISSING = 16;
    const BAD_MGT_KEY = 17; 
    const DEVICE_ID_ALREADY_EXISTS = 18;
    const IP_NOT_FOUND = 19;
    const TOO_MANY_USER_WITH_IP = 20;
    const INVALID_ARG = 21;
    const APPLICATION_NOT_FOUND = 22;
    const USER_NOT_FOUND = 23;
    const INVALID_TIMEZONE = 24;
    const INVALID_DATE = 25;
    const MAX_USAGE_REACHED = 26;
    const MEASURE_ALREADY_EXISTS = 27;
    const ALREADY_DEVICE_OWNER = 28;
    const INVALID_IP = 29;
    const INVALID_REFRESH_TOKEN = 30;
    const NOT_FOUND = 31;
    const BAD_PASSWORD = 32;
    const FORCE_ASSOCIATE = 33;
}

class NAScopes
{
    const SCOPE_READ_STATION = "read_station";
    const SCOPE_READ_THERM = "read_thermostat";
    const SCOPE_WRITE_THERM = "write_thermostat";
    static $validScopes = array(NAScopes::SCOPE_READ_STATION,NAScopes::SCOPE_WRITE_STATION,NAScopes::SCOPE_READ_THERM,NAScopes::SCOPE_WRITE_THERM);
}

class NAClientErrorCode
{
    const OAUTH_INVALID_GRANT = -1;
    const OAUTH_OTHER = -2;
}

class NAPublicConst
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

    const FEEL_LIKE_HUMIDEX_ALGO = 0;
    const FEEL_LIKE_HEAT_ALGO = 1;
    const FEEL_LIKE_NUMBER = 2;

    const KIND_READ_TIMELINE = 0;
    const KIND_NOT_READ_TIMELINE = 1;
    const KIND_BOTH_TIMELINE = 2;
}

class NAWifiRssiThreshold
{
    const RSSI_THRESHOLD_0 = 86;/*bad signal*/
    const RSSI_THRESHOLD_1 = 71;/*middle quality signal*/
    const RSSI_THRESHOLD_2 = 56;/*good signal*/
}

class NARadioRssiTreshold
{
    const RADIO_THRESHOLD_0 = 90;
    const RADIO_THRESHOLD_1 = 80;
    const RADIO_THRESHOLD_2 = 70;
    const RADIO_THRESHOLD_3 = 60;
}

class NAScheduleTime
{
    const WEEK_WAKEUP_TIME_DEFAULT = 420;
    const WEEK_SLEEP_TIME_DEFAULT = 1320;
    const WEEK_WORK_TIME_DEFAULT = 480;
    const WEEK_WORK_TIME_BACK_DEFAULT = 1140;
    const WEEK_WORK_LUNCH_TIME_DEFAULT = 720;
    const WEEK_WORK_LUNCH_TIME_BACK_DEFAULT = 810;
}


class NABatteryLevelIndoorModule
{
    /* Battery range: 6000 ... 4200 */
    const INDOOR_BATTERY_LEVEL_0 = 5640;/*full*/
    const INDOOR_BATTERY_LEVEL_1 = 5280;/*high*/
    const INDOOR_BATTERY_LEVEL_2 = 4920;/*medium*/
    const INDOOR_BATTERY_LEVEL_3 = 4560;/*low*/
    /* Below 4560: very low */
}

class NABatteryLevelModule
{
    /* Battery range: 6000 ... 3600 */
    const BATTERY_LEVEL_0 = 5500;/*full*/
    const BATTERY_LEVEL_1 = 5000;/*high*/
    const BATTERY_LEVEL_2 = 4500;/*medium*/
    const BATTERY_LEVEL_3 = 4000;/*low*/
    /* below 4000: very low */
}

class NABatteryLevelThermostat
{
    /* Battery range: 4500 ... 3000 */
    const THERMOSTAT_BATTERY_LEVEL_0 = 4100;/*full*/
    const THERMOSTAT_BATTERY_LEVEL_1 = 3600;/*high*/
    const THERMOSTAT_BATTERY_LEVEL_2 = 3300;/*medium*/
    const THERMOSTAT_BATTERY_LEVEL_3 = 3000;/*low*/
    /* below 3000: very low */
}

class NATimeBeforeDataExpire
{
    const TIME_BEFORE_UNKNONWN_THERMOSTAT = 7200;
    const TIME_BEFORE_UNKNONWN_STATION = 86400;
}


?>
