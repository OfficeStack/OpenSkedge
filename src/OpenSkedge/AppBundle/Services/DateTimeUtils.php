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
    public function timeStrToDateTime($timeString, $utc = false)
    {
        $timeArr = explode(":", date("H:i:s", strtotime($timeString)));
        if ($utc) {
            $time = new \DateTime("midnight today", new \DateTimeZone("UTC"));
        } else {
            $time = new \DateTime("midnight today");
        }
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
    public function getIndexFromTime($time, $utc = false)
    {
        if (empty($time) or (!is_string($time) and !$time instanceof \DateTime)) {
            throw new \InvalidArgumentException('Expected string or instance of DateTime');
        }

        if ($utc) {
            $dayStart = new \DateTime("midnight today", new \DateTimeZone("UTC"));
        } else {
            $dayStart = new \DateTime("midnight today");
        }

        if (is_string($time)) {
            $time = $this->timeStrToDateTime($time, $utc);
        }

        $index = (int)((($time->getTimestamp() - $dayStart->getTimestamp()) / 60) / 15);

        return $index;
    }

    /**
     * Convert a 15 minute chunk index and return a DateTime object.
     *
     * @param integer $day   A numerical day of the week (0-6)
     * @param integer $index An index refering to a 15 minute chunk in a day
     * @param boolean $utc   Return timezone UTC (default system)
     *
     * @return \DateTime
     */
    public function getDateTimeFromIndex($day, $index, $utc = false)
    {
        if (!is_int($day) or !is_int($index)) {
            throw new \InvalidArgumentException('Expected two integer arguments.');
        } elseif ($day < 0 or $day > 6) {
            throw new \OutOfRangeException('Excepted a day number from 0 to 6 (inclusive)');
        } elseif ($index < 0 or $index > 95) {
            throw new \OutOfRangeException('Excepted a 15 min chunk index from 0 to 95 (inclusive)');
        }

        $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        $fullMinutes = ($index * 15);
        if (!$utc) {
            $dt = new \DateTime("midnight this ".$days[$day]);
        } else {
            $dt = new \DateTime("midnight this ".$days[$day], new \DateTimeZone("UTC"));
        }
        $dt->setTime(0, $fullMinutes, 0);

        return $dt;
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

    /**
     * Get the intervals as \DateTime instance when a user is scheduled, clocked, etc. for a specific day
     *
     * @param string  $dayRecord    Time record string
     * @param integer $day          Day number (0 = Sunday, ..., 6 = Saturday)
     *
     * @return array
     */
    public function getDateTimeIntervals($dayRecord, $day, $utc = false)
    {
        $intervals = array();
        $firstIndex = strpos($dayRecord, '1'); // Inclusive
        $lastIndex = strrpos($dayRecord, '1')+1; // Exclusive

        // If there are no matches, end
        if ($firstIndex === false or $lastIndex === false) {
            return $intervals;
        }

        $prev = '0';
        // Go all the way to the element after the last match, or the end of the string (which ever is less).
        for ($i = $firstIndex; $i < min(96, $lastIndex+1); $i++) {
            if ($prev == '0' and $dayRecord[$i] == '1') {
                // A new interval starts here.
                $start = $i;
            } else if ($prev == '1' and $dayRecord[$i] == '0') {
                // An interval ends here. Add a DateTime pair to the $intervals array
                $intervals[] = array($this->getDateTimeFromIndex($day, $start, $utc), $this->getDateTimeFromIndex($day, $i+1, $utc));
            } else if ($prev == '1' and $dayRecord[$i] == '1' and $i == 95) {
                // If we reach the end of the string, set the end of the interval to tomorrow at midnight.
                $startDT = $this->getDateTimeFromIndex($day, $start, $utc);
                $endDT = clone $startDT;
                $endDT->modify("+1 day");
                $endDT->setTime(0, 0, 0);
                $intervals[] = array($startDT, $endDT);
            }
            $prev = $dayRecord[$i];
        }

        return $intervals;
    }
}
