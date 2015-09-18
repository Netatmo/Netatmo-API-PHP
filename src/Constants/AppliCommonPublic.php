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
    const MODULE_ALREADY_PAIRED = 34;
    const UNABLE_TO_EXECUTE = 35;
    const PROHIBITTED_STRING = 36;
    const CAMERA_NO_SPACE_AVAILABLE = 37;
    const PASSWORD_COMPLEXITY_TOO_LOW = 38;
    const TOO_MANY_CONNECTION_FAILURE = 39;
}

class NAClientErrorCode
{
    const OAUTH_INVALID_GRANT = -1;
    const OAUTH_OTHER = -2;
}

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
    static $defaultScopes = array(NAScopes::SCOPE_READ_STATION);
    static $validScopes = array(NAScopes::SCOPE_READ_STATION, NAScopes::SCOPE_READ_THERM, NAScopes::SCOPE_WRITE_THERM, NAScopes::SCOPE_READ_CAMERA, NAScopes::SCOPE_WRITE_CAMERA, NAScopes::SCOPE_ACCESS_CAMERA, NAScopes::SCOPE_READ_JUNE, NAScopes::SCOPE_WRITE_JUNE);
    // scope allowed to everyone (no need to be approved)
    static $basicScopes = array(NAScopes::SCOPE_READ_STATION, NAScopes::SCOPE_READ_THERM, NASCopes::SCOPE_WRITE_THERM, NAScopes::SCOPE_READ_CAMERA, NAScopes::SCOPE_WRITE_CAMERA, NAScopes::SCOPE_READ_JUNE, NAScopes::SCOPE_WRITE_JUNE);
}

class NAWebhook
{
    const UNIT_TEST = "test";
    const LOW_BATTERY = "low_battery";
    const BOILER_NOT_RESPONDING = "boiler_not_responding";
    const BOILER_RESPONDING = "boiler_responding";
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

    const UNIT_PRESSURE_MBAR = 0;
    const UNIT_PRESSURE_MERCURY = 1;
    const UNIT_PRESSURE_TORR = 2;
    const UNIT_PRESSURE_NUMBER = 3;

    const FEEL_LIKE_HUMIDEX_ALGO = 0;
    const FEEL_LIKE_HEAT_ALGO = 1;
    const FEEL_LIKE_NUMBER = 2;

    const KIND_READ_TIMELINE = 0;
    const KIND_NOT_READ_TIMELINE = 1;
    const KIND_BOTH_TIMELINE = 2;
}

class NAConstants
{
    const FAVORITES_MAX = 5;
}

/*
 * Defines the min and max values of the sensors.
 */
class NAStationSensorsMinMax
{
    const TEMP_MIN = -40;
    const TEMP_MAX = 60;
    const HUM_MIN = 1;
    const HUM_MAX = 99;
    const CO2_MIN = 300;
    const CO2_MAX = 4000;
    const PRESSURE_MIN = 700;
    const PRESSURE_MAX = 1300;
    const NOISE_MIN = 10;
    const NOISE_MAX = 120;
    const RAIN_MIN = 2;
    const RAIN_MAX = 300;
    const WIND_MIN = 5;
    const WIND_MAX = 150;
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

class NAWifiRssiThreshold
{
    const RSSI_THRESHOLD_0 = 86;/*bad signal*/
    const RSSI_THRESHOLD_1 = 71;/*middle quality signal*/
    const RSSI_THRESHOLD_2 = 56;/*good signal*/
}

class NARadioRssiTreshold
{
    const RADIO_THRESHOLD_0 = 90;/*low signal*/
    const RADIO_THRESHOLD_1 = 80;/*signal medium*/
    const RADIO_THRESHOLD_2 = 70;/*signal high*/
    const RADIO_THRESHOLD_3 = 60;/*full signal*/
}

class NABatteryLevelWindGaugeModule
{
    /* Battery range: 6000 ... 3950 */
    const WG_BATTERY_LEVEL_0 = 5590;/*full*/
    const WG_BATTERY_LEVEL_1 = 5180;/*high*/
    const WG_BATTERY_LEVEL_2 = 4770;/*medium*/
    const WG_BATTERY_LEVEL_3 = 4360;/*low*/
    /* below 4360: very low */
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
    const TIME_BEFORE_UNKNONWN_STATION = 7200;
    const TIME_BEFORE_UNKNONWN_CAMERA = 46800; // 13h
}

class NAHeatingEnergy
{
    const THERMOSTAT_HEATING_ENERGY_UNKNOWN = "unknown";
    const THERMOSTAT_HEATING_ENERGY_GAS = "gas";
    const THERMOSTAT_HEATING_ENERGY_OIL = "oil";
    const THERMOSTAT_HEATING_ENERGY_WOOD = "wood";
    const THERMOSTAT_HEATING_ENERGY_ELEC = "elec";
    const THERMOSTAT_HEATING_ENERGY_PAC = "pac";
    const THERMOSTAT_HEATING_ENERGY_SUNHYBRID = "sunhybrid";
}

class NAHeatingType
{
    const THERMOSTAT_HEATING_TYPE_UNKNOWN = "unknown";
    const THERMOSTAT_HEATING_TYPE_SUBFLOOR = "subfloor";
    const THERMOSTAT_HEATING_TYPE_RADIATOR = "radiator";
}

class NAHomeType
{
    const THERMOSTAT_HOME_TYPE_UNKNOWN = "unknown";
    const THERMOSTAT_HOME_TYPE_HOUSE = "house";
    const THERMOSTAT_HOME_TYPE_FLAT = "flat";
}

class NAPluvioLevel // en mm
{
    const RAIN_NULL = 0.1; // <
    const RAIN_WEAK = 3; // <
    const RAIN_MIDDLE = 8; // <
    const RAIN_STRONG = 15; // or > 8, don't use this value
}

class NAPluvioCalibration
{
    const RAIN_SCALE_MIN = 0.01;
    const RAIN_SCALE_MAX = 0.25;
    const RAIN_SCALE_ML_MIN = 0;
    const RAIN_SCALE_ML_MAX = 3;
}

//CAMERA SPECIFIC DATA
class NACameraEventType
{
    const CET_PERSON = "person";
    const CET_PERSON_AWAY = "person_away";
    const CET_MODEL_IMPROVED = "model_improved";
    const CET_MOVEMENT = "movement";
    const CET_CONNECTION = "connection";
    const CET_DISCONNECTION = "disconnection";
    const CET_ON = "on";
    const CET_OFF = "off";
    const CET_END_RECORDING = "end_recording";
    const CET_LIVE = "live_rec";
    const CET_BOOT = "boot";
    const CET_SD = "sd";
    const CET_ALIM = "alim";
}

class NACameraEventInfo
{
    const CEI_ID = "id";
    const CEI_TYPE = "type";
    const CEI_TIME = "time";
    const CEI_PERSON_ID = "person_id";
    const CEI_SNAPSHOT = "snapshot";
    const CEI_VIDEO_ID = "video_id";
    const CEI_VIDEO_STATUS = "video_status";
    const CEI_CAMERA_ID = "camera_id";
    const CEI_MESSAGE = "message";
    const CEI_SUB_TYPE = "sub_type";
    const CEI_IS_ARRIVAL = "is_arrival";
    const CEI_ALARM_ID = "alarm_id";
    const CEI_ALARM_TYPE = "alarm_type";
}

class NACameraPersonInfo
{
    const CPI_ID = "id";
    const CPI_LAST_SEEN = "last_seen";
    const CPI_FACE = "face";
    const CPI_OUT_OF_SIGHT = "out_of_sight";
    const CPI_PSEUDO = "pseudo";
    const CPI_IS_CURRENT_USER = "is_current_user";
}

class NACameraImageInfo
{
    const CII_ID = "id";
    const CII_VERSION = "version";
    const CII_KEY = "key";
}

class NACameraHomeInfo
{
    const CHI_ID = "id";
    const CHI_NAME = "name";
    const CHI_PLACE = "place";
    const CHI_PERSONS = "persons";
    const CHI_EVENTS = "events";
    const CHI_CAMERAS = "cameras";
}

class NACameraInfo
{
    const CI_ID = "id";
    const CI_NAME = "name";
    const CI_LIVE_URL = "live_url";
    const CI_STATUS = "status";
    const CI_SD_STATUS = "sd_status";
    const CI_ALIM_STATUS = "alim_status";
    const CI_IS_LOCAL = "is_local";
    const CI_VPN_URL = "vpn_url";
}

class NACameraStatus
{
    const CS_ON = "on";
    const CS_OFF = "off" ;
    const CS_DISCONNECTED = "disconnected";
}

class APIResponseFields
{
    const APIRF_SYNC_ORDER = "sync";
    const APIRF_KEEP_RECORD_ORDER = "keep_record";
    const APIRF_VIDEO_ID = "video_id";
    const APIRF_SYNC_ORDER_LIST = "sync_order_list";
    const APIRF_EVENTS_LIST = "events_list";
    const APIRF_PERSONS_LIST = "persons_list";
    const APIRF_HOMES = "homes";
    const APIRF_USER = "user";
    const APIRF_GLOBAL_INFO = "global_info";
}


class NACameraVideoStatus
{
    const CVS_RECORDING = "recording";
    const CVS_DELETED = "deleted";
    const CVS_AVAILABLE = "available";
    const CVS_ERROR = "error";
}

class NACameraSDEvent
{
    const CSDE_ABSENT = 1;
    const CSDE_INSERTED = 2;
    const CSDE_FORMATED = 3;
    const CSDE_OK = 4;
    const CSDE_DEFECT = 5;
    const CSDE_INCOMPATIBLE = 6;
    const CSDE_TOO_SMALL = 7;

    static $issueEvents = array(NACameraSDEvent::CSDE_ABSENT, NACameraSDEvent::CSDE_DEFECT, NACameraSDEvent::CSDE_INCOMPATIBLE, NACameraSDEvent::CSDE_TOO_SMALL);
}

class NACameraAlimSubStatus
{
    const CASS_DEFECT = 1;
    const CASS_OK = 2;
}

class NAThermZone {
const THERMOSTAT_SCHEDULE_SLOT_DAY      = 0x00;
const THERMOSTAT_SCHEDULE_SLOT_NIGHT    = 0x01;
const THERMOSTAT_SCHEDULE_SLOT_AWAY     = 0x02;
const THERMOSTAT_SCHEDULE_SLOT_HG       = 0x03;
const THERMOSTAT_SCHEDULE_SLOT_PERSO    = 0x04;
const THERMOSTAT_SCHEDULE_SLOT_ECO      = 0x05;
const THERMOSTAT_SCHEDULE_HOT_WATER_ON  = 0x06;
const THERMOSTAT_SCHEDULE_HOT_WATER_OFF = 0x07;
}

?>
