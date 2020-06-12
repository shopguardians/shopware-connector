<?php
/**
 * @author Hans Wellenschritt hans.wellenschritt@active-value.de
 * @copyright active value GmbH
 * Date: 09.06.20
 * Time: 10:10
 */

namespace Shopguardians\Shared;

use DateTime;

class TimeUtils
{

    public static function getBeginningOfToday()
    {
        $date = new \DateTime();
        $date->modify('today');
        return $date;
    }

    public static function getBeginningOfTomorrow()
    {
        $date = self::getBeginningOfToday();
        $date->modify('+ 1day');
        return $date;
    }

    public static function getDateMinusDays($daysToSubtract)
    {
        $date = new \DateTime();
        $date->modify('- ' . $daysToSubtract . 'days');
        return $date;
    }

    public static function getNow()
    {
        return new \DateTime();
    }

}