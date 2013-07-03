<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\SchedulePeriod;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/SchedulePeriod
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class SchedulePeriodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Run a test to ensure instantiation of SchedulePeriod objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $schedulePeriod = new SchedulePeriod();

        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\SchedulePeriod', $schedulePeriod);
    }

    /**
     * Run tests to ensure the output is correct for set/getStartTime
     *
     * @return void
     */
    public function testStartTime()
    {
        $schedulePeriod = new SchedulePeriod();

        $startTime = new \DateTime("Jan 28, 2013", new \DateTimeZone("UTC"));

        $schedulePeriod->setStartTime($startTime);
        $this->assertEquals($startTime->getTimestamp(), $schedulePeriod->getStartTime()->getTimestamp());
    }

    /**
     * Run tests to ensure the output is correct for set/getEndTime
     *
     * @return void
     */
    public function testEndTime()
    {
        $schedulePeriod = new SchedulePeriod();

        $endTime = new \DateTime("May 18, 2013", new \DateTimeZone("UTC"));

        $schedulePeriod->setEndTime($endTime);
        $this->assertEquals($endTime->getTimestamp(), $schedulePeriod->getEndTime()->getTimestamp());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getSchedule(s)
     *
     * @return void
     */
    public function testSchedule()
    {
        $testSchedule = $this->getMock('\OpenSkedge\AppBundle\Entity\Schedule');

        $schedulePeriod = new SchedulePeriod();
        $schedulePeriod->addSchedule($testSchedule);

        $schedules = $schedulePeriod->getSchedules();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Schedule', $schedules[0]);

        $schedulePeriod->removeSchedule($testSchedule);
        $this->assertTrue($schedulePeriod->getSchedules()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getSchedule(s)
     *
     * @return void
     */
    public function testAvailabilitySchedule()
    {
        $testAvailSchedule = $this->getMock('\OpenSkedge\AppBundle\Entity\AvailabilitySchedule');

        $schedulePeriod = new SchedulePeriod();
        $schedulePeriod->addAvailabilitySchedule($testAvailSchedule);

        $availSchedules = $schedulePeriod->getAvailabilitySchedules();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\AvailabilitySchedule', $availSchedules[0]);

        $schedulePeriod->removeAvailabilitySchedule($testAvailSchedule);
        $this->assertTrue($schedulePeriod->getAvailabilitySchedules()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getLateShift(s)
     *
     * @return void
     */
    public function testLateShift()
    {
        $lateShift = $this->getMock('\OpenSkedge\AppBundle\Entity\LateShift');

        $schedulePeriod = new SchedulePeriod();
        $schedulePeriod->addLateShift($lateShift);

        $lateShifts = $schedulePeriod->getLateShifts();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\LateShift', $lateShifts[0]);

        $schedulePeriod->removeLateShift($lateShift);
        $this->assertTrue($schedulePeriod->getLateShifts()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getShift(s)
     *
     * @return void
     */
    public function testShift()
    {
        $shift = $this->getMock('\OpenSkedge\AppBundle\Entity\Shift');

        $schedulePeriod = new SchedulePeriod();
        $schedulePeriod->addShift($shift);

        $shifts = $schedulePeriod->getShifts();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Shift', $shifts[0]);

        $schedulePeriod->removeShift($shift);
        $this->assertTrue($schedulePeriod->getShifts()->isEmpty());
    }
}
