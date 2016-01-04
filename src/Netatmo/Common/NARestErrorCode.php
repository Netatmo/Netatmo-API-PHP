<?php

namespace Netatmo\Common;

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
?>
