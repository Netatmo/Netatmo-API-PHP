<?php
/**
* Wrapper ensuring backward compatibility with older SDK versions
*
* DEPRECATED
*
* You must not use this class
*
*/

require_once dirname(dirname(__FILE__)) . "/Netatmo/autoload.php";

class NAObject extends Netatmo\Objects\NAObject
{
}

class NAObjectWithPicture extends Netatmo\Objects\NAObjectWithPicture
{
}

?>
