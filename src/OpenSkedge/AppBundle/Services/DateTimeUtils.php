<?php

namespace OpenSkedge\AppBundle\Services;

class DateTimeUtils
{
    public function timeStrToDateTime($timeString)
    {
        $timeArr = explode(":", date("H:i:s", strtotime($timeString)));
        $time = new \DateTime("midnight today", new \DateTimeZone("UTC"));
        $time->setTime($timeArr[0], $timeArr[1], $timeArr[2]);

        return $time;
    }

    public function getIndexFromTime($time)
    {
        if (!is_string($time) && !$time instanceof \DateTime) {
            throw new InvalidArgumentException('Expected string or instance of DateTime');
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
        $appSettings = $this->get('appsettings')->getAppSettings();

        if ($clock) {
            $day = $appSettings->getWeekStartDayClock();
        } else {
            $day = $appSettings->getWeekStartDay();
        }

        $firstDay = idate('w', strtotime($day));
        $offset = 7 - $firstDay;
        $ret = clone $date;
        $ret->modify(-(($date->format('w') + $offset) % 7) . 'days');
        $ret->modify('midnight');
        return $ret;
    }
}
