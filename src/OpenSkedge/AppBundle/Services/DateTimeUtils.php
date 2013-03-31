<?php

namespace OpenSkedge\AppBundle\Services;

class DateTimeUtils
{
    public function timeStrToDateTime($timeString)
    {
        $timeArr = explode(":", $timeString);
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
}
