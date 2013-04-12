<?php

namespace OpenSkedge\AppBundle\Services;

/**
 * A service class for functions commonly used by OpenSkedge
 *
 * @category Services
 * @package  OpenSkedge\AppBundle\Services
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class DateTimeUtils
{
    /**
     * @var \OpenSkedge\AppBundle\Entity\Settings
     */
    protected $appSettings;

    /**
     * Inject services into DateTimeUtils
     *
     * @param AppSettingsService $appSettingsService AppSettingsService object
     *
     * @return void
     */
    public function __construct(AppSettingsService $appSettingsService)
    {
        $this->appSettings = $appSettingsService->getAppSettings();
    }

    /**
     * Converts a string containing a time to a \DateTime object
     *
     * @param string $timeString A string containing a time such as "4:00 am" or "23:00"
     *
     * @return \DateTime
     */
    public function timeStrToDateTime($timeString)
    {
        $timeArr = explode(":", date("H:i:s", strtotime($timeString)));
        $time = new \DateTime("midnight today", new \DateTimeZone("UTC"));
        $time->setTime($timeArr[0], $timeArr[1], $timeArr[2]);

        return $time;
    }

    /**
     * Round a \DateTime object or string to the nearest 15 minute chunk and return
     * the index out of 95. (There are 96 fifteen minute chunks in a 24-hour day)
     *
     * @param \DateTime|string $time A given time
     *
     * @return integer
     */
    public function getIndexFromTime($time)
    {
        if (empty($time) or (!is_string($time) and !$time instanceof \DateTime)) {
            throw new \InvalidArgumentException('Expected string or instance of DateTime');
        }

        $dayStart = new \DateTime("midnight today", new \DateTimeZone("UTC"));

        if (is_string($time)) {
            $time = $this->timeStrToDateTime($time);
        }

        $index = (int)((($time->getTimestamp() - $dayStart->getTimestamp()) / 60) / 15);

        return $index;
    }

    /**
     * Take a \DateTime object and get the date of the first day of its week
     * dependant on global application settings.
     *
     * @param \DateTime $date  A given date
     * @param boolean   $clock The locale week start day or time clock week start day
     *
     * @return \DateTime
     */
    public function getFirstDayOfWeek(\DateTime $date, $clock = false)
    {
        if ($clock) {
            $day = $this->appSettings->getWeekStartDayClock();
        } else {
            $day = $this->appSettings->getWeekStartDay();
        }

        $firstDay = idate('w', strtotime($day));
        $offset = 7 - $firstDay;
        $ret = clone $date;
        $ret->modify(-(($date->format('w') + $offset) % 7) . 'days');
        $ret->modify('midnight');
        return $ret;
    }
}
