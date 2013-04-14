<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\ArchivedClock;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/ArchivedClock
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ArchivedClockTest extends \PHPUnit_Framework_TestCase
{
    private $_emptyRec = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";

    /**
     * Run a test to ensure instatiation of ArchivedClock objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $archivedClock = new ArchivedClock();

        $this->assertEquals($this->_emptyRec, $archivedClock->getSun());
        $this->assertEquals($this->_emptyRec, $archivedClock->getMon());
        $this->assertEquals($this->_emptyRec, $archivedClock->getTue());
        $this->assertEquals($this->_emptyRec, $archivedClock->getWed());
        $this->assertEquals($this->_emptyRec, $archivedClock->getThu());
        $this->assertEquals($this->_emptyRec, $archivedClock->getFri());
        $this->assertEquals($this->_emptyRec, $archivedClock->getSat());
    }

    /**
     * Run tests to ensure the output is correct for set/getDay, set/getMon, etc.
     *
     * @return void
     */
    public function testDay()
    {
        $archivedClock = new ArchivedClock();

        $archivedClock->setDay(0, "100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getSun());

        $archivedClock->setDay(1, "110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getMon());

        $archivedClock->setDay(2, "111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getTue());

        $archivedClock->setDay(3, "111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getWed());

        $archivedClock->setDay(4, "111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getThu());

        $archivedClock->setDay(5, "111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getFri());

        $archivedClock->setDay(6, "111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getSat());

        $this->assertEquals("100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getDay(0));

        $this->assertEquals("110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getDay(1));

        $this->assertEquals("111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getDay(2));

        $this->assertEquals("111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getDay(3));

        $this->assertEquals("111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getDay(4));

        $this->assertEquals("111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getDay(5));

        $this->assertEquals("111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $archivedClock->getDay(6));
    }

    /**
     * Run tests to ensure the output is correct for set/getDayOffset
     *
     * @return void
     */
    public function testOffset()
    {
        $archivedClock = new ArchivedClock();

        $archivedClock->setDayOffset(0, 38, 1);
        $this->assertEquals(1, $archivedClock->getDayOffset(0, 38));

        $archivedClock->setDayOffset(1, 93, 1);
        $this->assertEquals(1, $archivedClock->getDayOffset(1, 93));

        $archivedClock->setDayOffset(2, 61, 1);
        $this->assertEquals(1, $archivedClock->getDayOffset(2, 61));

        $archivedClock->setDayOffset(3, 11, 1);
        $this->assertEquals(1, $archivedClock->getDayOffset(3, 11));

        $archivedClock->setDayOffset(4, 87, 1);
        $this->assertEquals(1, $archivedClock->getDayOffset(4, 87));

        $archivedClock->setDayOffset(5, 74, 1);
        $this->assertEquals(1, $archivedClock->getDayOffset(5, 74));

        $archivedClock->setDayOffset(6, 52, 1);
        $this->assertEquals(1, $archivedClock->getDayOffset(6, 52));
    }

    /**
     * Run tests to ensure the output is correct for set/getUser
     *
     * @return void
     */
    public function testUser()
    {
        $user = $this->getMock('\OpenSkedge\AppBundle\Entity\User');

        $archivedClock = new ArchivedClock();

        $archivedClock->setUser($user);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\User', $archivedClock->getUser());
    }

    /**
     * Run tests to ensure the output is correct for set/getWeek
     *
     * @return void
     */
    public function testWeek()
    {
        $week = new \DateTime("Apr 7, 2013", new \DateTimeZone("UTC"));

        $archivedClock = new ArchivedClock();

        $archivedClock->setWeek($week);
        $this->assertEquals($week->getTimestamp(), $archivedClock->getWeek()->getTimestamp());
    }
}
