<?php

namespace Netatmo\Clients;

/**
 * NETATMO PUBLIC API PHP CLIENT
 *
 * For more details upon NETATMO API, please check https://dev.netatmo.com/doc
 * @author Originally written by Thierry Pauwels
 */
class NAPublicApiClient extends NAApiClient
{

    /*
     * @type PUBLIC API
     * @param string $device_id
     * @return array of devices
     * @brief Method used to retrieve data for the given Thermostat or all the thermostats belonging to the user
     */
    public function getData($lat_sw, $lat_ne, $lon_sw, $lon_ne, $required_data = NULL, $filter = NULL)
    {
        $url = BACKEND_SERVICES_URI.'/getpublicdata?access_token='.$this->access_token
				                           .'&lat_sw='.$lat_sw.'&lon_sw='.$lon_sw
																	 .'&lat_ne='.$lat_ne.'&lon_ne='.$lon_ne;
        if (!is_null($required_data)) $url = $url.'&required_data='.$required_data;
        if (!is_null($filter))        $url = $url.'&filter='.$filter;

        return $this->makeRequest($url);
    }

}
?>
