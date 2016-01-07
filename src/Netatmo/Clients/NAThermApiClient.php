<?php

namespace Netatmo\Clients;

/**
 * NETATMO THERMOSTAT API PHP CLIENT
 *
 * For more details upon NETATMO API, please check https://dev.netatmo.com/doc
 * @author Originally written by Enzo Macri <enzo.macri@netatmo.com>
 */
class NAThermApiClient extends NAApiClient
{

    private function setThermPoint($device_id, $module_id, $mode, $endtime = NULL, $temp = NULL)
    {
        $params = array('device_id' => $device_id,
                        'module_id' => $module_id,
                        'setpoint_mode' => $mode);

        if(!is_null($endtime)) $params['setpoint_endtime'] = $endtime;
        if(!is_null($temp)) $params['setpoint_temp'] = $temp;

        return $this->api('setthermpoint', 'POST', $params);
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @return array of devices
     * @brief Method used to retrieve data for the given Thermostat or all the thermostats belonging to the user
     */
    public function getData($device_id = NULL)
    {
        $params = array();
        if(!is_null($device_id)) $params['device_id'] = $device_id;

        return $this->api('getthermostatsdata', 'GET', $params);
    }


    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $module_id
     * @param timestamp (utc) $endtime (optional)
     * @brief Sets the given thermostat to away mode until the given date or until change.
     */
    public function setToAwayMode($device_id, $module_id, $endtime = NULL)
    {
        return $this->setThermPoint($device_id, $module_id, 'away', $endtime);
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $module_id
     * @param timestamp (utc) $endtime (optional)
     * @brief Sets the given thermostat to frost-guard mode until the given date or until change
     */
    public function setToFrostGuardMode($device_id, $module_id, $endtime = NULL)
    {
        return $this->setThermPoint($device_id, $module_id, 'hg', $endtime);
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $module_id
     * @brief Turn off the given thermostat
     */
    public function turnOff($device_id, $module_id)
    {
        return $this->setThermPoint($device_id, $module_id, 'off');
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $module_id
     * @brief Order the given thermostat to follow its schedule
     */
    public function setToProgramMode($device_id, $module_id)
    {
        return $this->setThermPoint($device_id, $module_id, 'program');
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $module_id
     * @param float $temp
     * @param timestamp (utc) $endtime (optional)
     * @brief Sets a manual temperature to the given thermostat for a specified amount of time
     */
    public function setToManualMode($device_id, $module_id, $temp, $endtime = NULL)
    {
        return $this->setThermPoint($device_id, $module_id, 'manual', $endtime, $temp);
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $module_id
     * @param timestamp(utc) $endtime
     * @brief Order the given thermostat to heat to its max temperature
     */
    public function setToMaxMode($device_id, $module_id, $endtime)
    {
         return $this->setThermPoint($device_id, $module_id, 'max', $endtime);
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $module_id
     * @param array  $zones
     * @param array $timetable
     * @param string $name
     * @brief Create a new heating schedule for the given thermostat
     */
    public function createSchedule($device_id, $module_id, $zones, $timetable, $name)
    {
        $params = array('device_id' => $device_id,
                        'module_id' => $module_id,
                        'zones' => $zones,
                        'timetable' => $timetable,
                        'name' => $name);
        return $this->api('createnewschedule', 'POST', $params);
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $module_id
     * @param string $schedule_id
     * @brief switch to the given existing heating schedule
     */
    public function switchSchedule($device_id, $module_id, $schedule_id)
    {
        $params = array('device_id' => $device_id,
                        'module_id' => $module_id,
                        'schedule_id' => $schedule_id);
        return $this->api('switchschedule', 'POST', $params);
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $device_id
     * @param string $schedule_id
     * @param string $name
     * @brief Rename an existing heating schedule
     */
    public function renameSchedule($device_id, $module_id, $schedule_id, $name)
    {
        $params= array('device_id' => $device_id,
                       'module_id' => $module_id,
                       'schedule_id' => $schedule_id,
                       'name' => $name);
        return $this->api('renameschedule', 'POST', $params);
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $module_id
     * @param string $schedule_id
     * @brief Delete the given heating schedule. Beware, there should always be at least one schedule left for the device.
     */
    public function deleteSchedule($device_id, $module_id, $schedule_id)
    {
        $params = array('device_id' => $device_id,
                        'module_id' => $module_id,
                        'schedule_id' => $schedule_id);
        return $this->api('deleteschedule', 'POST', $params);
    }

    /*
     * @type PRIVATE & PARTNER API
     * @param string $device_id
     * @param string $module_id
     * @param array $zones
     * @param array $timetable
     * @brief change the thermostat's heating schedule the given one
     */
    public function syncSchedule($device_id, $module_id, $zones, $timetable)
    {
        $params = array('device_id' => $device_id,
                        'module_id' => $module_id,
                        'zones' => $zones,
                        'timetable' => $timetable);
        return $this->api('syncschedule', 'POST', $params);
    }

   /*
    * @type PUBLIC, PRIVATE & PARTNER API
    * @param string $device_id
    * @param string $module_id (optional) if specified will retrieve the module's measurements, else it will retrieve the main device's measurements
    * @param string scale : interval of time between two measurements. Allowed values : max, 30min, 1hour, 3hours, 1day, 1week, 1month
    * @param string type : type of measurements you wanna retrieve. Ex : "Sp_Temperature, Temperature".
    * @param timestamp (utc) $start (optional) : starting timestamp of requested measurements
    * @param timestamp (utc) $end (optional) : ending timestamp of requested measurements.
    * @param int $limit (optional) : limits numbers of measurements returned (default & max : 1024)
    * @param bool $optimize (optional) : optimize the bandwith usage if true. Optimize = FALSE enables an easier result parsing
    * @param bool $realtime (optional) : Remove time offset (+scale/2) for scale bigger than max
    * @return array of measures and timestamp
    * @brief Method used to retrieve specifig measures of the given weather station
    */

    public function getMeasure($device_id, $module_id = NULL, $scale, $type, $start = NULL, $end = NULL, $limit = NULL, $optimize = NULL, $realtime = NULL)
    {
        $params = array('device_id' => $device_id,
                        'scale' => $scale,
                        'type' => $type);

        $optionals = array('module_id' => $module_id,
                           'date_begin' => $start,
                           'date_end' => $end,
                           'limit' => $limit,
                           'optimize' => $optimize,
                           'real_time' => $realtime);
        foreach($optionals as $key => $value)
        {
            if(!is_null($value)) $params[$key] = $value;
        }

        return $this->api('getmeasure', 'GET', $params);
    }
}
?>
