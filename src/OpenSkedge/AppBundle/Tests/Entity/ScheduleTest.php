<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\Schedule;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/Schedule
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ScheduleTest extends \PHPUnit_Framework_TestCase
{
    private $_emptyRec = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";

    /**
     * Run a test to ensure instantiation of Schedule objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $schedule = new Schedule();

        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Schedule', $schedule);

        $this->assertEquals($this->_emptyRec, $schedule->getSun());
        $this->assertEquals($this->_emptyRec, $schedule->getMon());
        $this->assertEquals($this->_emptyRec, $schedule->getTue());
        $this->assertEquals($this->_emptyRec, $schedule->getWed());
        $this->assertEquals($this->_emptyRec, $schedule->getThu());
        $this->assertEquals($this->_emptyRec, $schedule->getFri());
        $this->assertEquals($this->_emptyRec, $schedule->getSat());
    }

    /**
     * Run tests to ensure the output is correct for set/getDay, set/getMon, etc.
     *
     * @return void
     */
    public function testDay()
    {
        $schedule = new Schedule();

        $schedule->setDay(0, "100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getSun());

        $schedule->setDay(1, "110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getMon());

        $schedule->setDay(2, "111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getTue());

        $schedule->setDay(3, "111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getWed());

        $schedule->setDay(4, "111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getThu());

        $schedule->setDay(5, "111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getFri());

        $schedule->setDay(6, "111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getSat());

        $this->assertEquals("100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getDay(0));

        $this->assertEquals("110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getDay(1));

        $this->assertEquals("111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getDay(2));

        $this->assertEquals("111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getDay(3));

        $this->assertEquals("111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getDay(4));

        $this->assertEquals("111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getDay(5));

        $this->assertEquals("111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $schedule->getDay(6));
    }

    /**
     * Run tests to ensure the output is correct for set/getDayOffset
     *
     * @return void
     */
    public function testOffset()
    {
        $schedule = new Schedule();

        $schedule->setDayOffset(0, 38, 1);
        $this->assertEquals(1, $schedule->getDayOffset(0, 38));

        $schedule->setDayOffset(1, 93, 1);
        $this->assertEquals(1, $schedule->getDayOffset(1, 93));

        $schedule->setDayOffset(2, 61, 1);
        $this->assertEquals(1, $schedule->getDayOffset(2, 61));

        $schedule->setDayOffset(3, 11, 1);
        $this->assertEquals(1, $schedule->getDayOffset(3, 11));

        $schedule->setDayOffset(4, 87, 1);
        $this->assertEquals(1, $schedule->getDayOffset(4, 87));

        $schedule->setDayOffset(5, 74, 1);
        $this->assertEquals(1, $schedule->getDayOffset(5, 74));

        $schedule->setDayOffset(6, 52, 1);
        $this->assertEquals(1, $schedule->getDayOffset(6, 52));
    }

    /**
     * Run tests to ensure the output is correct for set/getNotes
     *
     * @return void
     */
    public function testNotes()
    {
        $schedule = new Schedule();

        $schedule->setNotes("Some notes and stuff!");
        $this->assertEquals("Some notes and stuff!", $schedule->getNotes());
    }

    /**
     * Run tests to ensure the output is correct for set/getUser
     *
     * @return void
     */
    public function testUser()
    {
        $user = $this->getMock('\OpenSkedge\AppBundle\Entity\User');

        $schedule = new Schedule();

        $schedule->setUser($user);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\User', $schedule->getUser());
    }

    /**
     * Run tests to ensure the output is correct for set/getPosition
     *
     * @return void
     */
    public function testPosition()
    {
        $position = $this->getMock('\OpenSkedge\AppBundle\Entity\Position');

        $schedule = new Schedule();

        $schedule->setPosition($position);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Position', $schedule->getPosition());
    }

    /**
     * Run tests to ensure the output is correct for set/getSchedulePeriod
     *
     * @return void
     */
    public function testSchedulePeriod()
    {
        $schedulePeriod = $this->getMock('\OpenSkedge\AppBundle\Entity\SchedulePeriod');

        $schedule = new Schedule();

        $schedule->setSchedulePeriod($schedulePeriod);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\SchedulePeriod', $schedule->getSchedulePeriod());
    }
}
