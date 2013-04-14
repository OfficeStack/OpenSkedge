<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\AvailabilitySchedule;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/AvailabilitySchedule
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class AvailabilityScheduleTest extends \PHPUnit_Framework_TestCase
{
    private $_emptyRec = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";

    /**
     * Run a test to ensure instatiation of AvailabilitySchedule objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $availSchedule = new AvailabilitySchedule();

        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\AvailabilitySchedule', $availSchedule);

        $this->assertEquals($this->_emptyRec, $availSchedule->getSun());
        $this->assertEquals($this->_emptyRec, $availSchedule->getMon());
        $this->assertEquals($this->_emptyRec, $availSchedule->getTue());
        $this->assertEquals($this->_emptyRec, $availSchedule->getWed());
        $this->assertEquals($this->_emptyRec, $availSchedule->getThu());
        $this->assertEquals($this->_emptyRec, $availSchedule->getFri());
        $this->assertEquals($this->_emptyRec, $availSchedule->getSat());
    }

    /**
     * Run tests to ensure the output is correct for set/getDay, set/getMon, etc.
     *
     * @return void
     */
    public function testDay()
    {
        $availSchedule = new AvailabilitySchedule();

        $availSchedule->setDay(0, "100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getSun());

        $availSchedule->setDay(1, "110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getMon());

        $availSchedule->setDay(2, "111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getTue());

        $availSchedule->setDay(3, "111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getWed());

        $availSchedule->setDay(4, "111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getThu());

        $availSchedule->setDay(5, "111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getFri());

        $availSchedule->setDay(6, "111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getSat());

        $this->assertEquals("100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getDay(0));

        $this->assertEquals("110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getDay(1));

        $this->assertEquals("111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getDay(2));

        $this->assertEquals("111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getDay(3));

        $this->assertEquals("111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getDay(4));

        $this->assertEquals("111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getDay(5));

        $this->assertEquals("111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $availSchedule->getDay(6));
    }

    /**
     * Run tests to ensure the output is correct for set/getDayOffset
     *
     * @return void
     */
    public function testOffset()
    {
        $availSchedule = new AvailabilitySchedule();

        $availSchedule->setDayOffset(0, 38, 1);
        $this->assertEquals(1, $availSchedule->getDayOffset(0, 38));

        $availSchedule->setDayOffset(1, 93, 1);
        $this->assertEquals(1, $availSchedule->getDayOffset(1, 93));

        $availSchedule->setDayOffset(2, 61, 1);
        $this->assertEquals(1, $availSchedule->getDayOffset(2, 61));

        $availSchedule->setDayOffset(3, 11, 1);
        $this->assertEquals(1, $availSchedule->getDayOffset(3, 11));

        $availSchedule->setDayOffset(4, 87, 1);
        $this->assertEquals(1, $availSchedule->getDayOffset(4, 87));

        $availSchedule->setDayOffset(5, 74, 1);
        $this->assertEquals(1, $availSchedule->getDayOffset(5, 74));

        $availSchedule->setDayOffset(6, 52, 1);
        $this->assertEquals(1, $availSchedule->getDayOffset(6, 52));
    }

    /**
     * Run tests to ensure the output is correct for set/getNotes
     *
     * @return void
     */
    public function testNotes()
    {
        $availSchedule = new AvailabilitySchedule();

        $availSchedule->setNotes("Some notes and stuff!");
        $this->assertEquals("Some notes and stuff!", $availSchedule->getNotes());
    }

    /**
     * Run tests to ensure the output is correct for set/getUser
     *
     * @return void
     */
    public function testUser()
    {
        $user = $this->getMock('\OpenSkedge\AppBundle\Entity\User');

        $availSchedule = new AvailabilitySchedule();

        $availSchedule->setUser($user);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\User', $availSchedule->getUser());
    }

    /**
     * Run tests to ensure the output is correct for set/getSchedulePeriod
     *
     * @return void
     */
    public function testSchedulePeriod()
    {
        $schedulePeriod = $this->getMock('\OpenSkedge\AppBundle\Entity\SchedulePeriod');

        $availSchedule = new AvailabilitySchedule();

        $availSchedule->setSchedulePeriod($schedulePeriod);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\SchedulePeriod', $availSchedule->getSchedulePeriod());
    }
}
