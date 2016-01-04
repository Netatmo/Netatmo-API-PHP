<?php

namespace Netatmo\Objects;

use Netatmo\Common\NACameraImageInfo;

abstract class NAObjectWithPicture extends NAObject
{
    public function getPictureURL($picture, $baseURI = 'https://api.netatmo.com/api/getcamerapicture')
    {
        if(isset($picture[NACameraImageInfo::CII_ID]) && isset($picture[NACameraImageInfo::CII_KEY]))
        {
            return $baseURI.'?image_id='.$picture[NACameraImageInfo::CII_ID].'&key='.$picture[NACameraImageInfo::CII_KEY];
        }
        else return NULL;

    }

}

?>
