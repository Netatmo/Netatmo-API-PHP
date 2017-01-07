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
     * @param float $lat_sw Latitude of SW corner
     * @param float $lat_ne Latitude of NE corner
     * @param float $lon_sw Longitude of SW corner
     * @param float $lon_ne Longitude of NE corner
		 * @param string $required_data Comma separated list of measurements that returned points should offer (optional)
		 * @param string $filter (optional)
     * @return Array of measurements as provided by the getpublicdata API from netatmo website
     * @brief Method uses to retrieve public measurement data from Netatmo website
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
