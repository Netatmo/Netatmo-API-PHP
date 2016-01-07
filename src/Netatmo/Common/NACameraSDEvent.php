<?php

namespace Netatmo\Common;

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

?>
