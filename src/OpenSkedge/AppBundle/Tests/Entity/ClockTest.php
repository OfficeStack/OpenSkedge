<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\Clock;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/Clock
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ClockTest extends \PHPUnit_Framework_TestCase
{
    private $_emptyRec = "000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";

    /**
     * Run a test to ensure instantiation of Clock objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $clock = new Clock();

        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Clock', $clock);

        $this->assertEquals($this->_emptyRec, $clock->getSun());
        $this->assertEquals($this->_emptyRec, $clock->getMon());
        $this->assertEquals($this->_emptyRec, $clock->getTue());
        $this->assertEquals($this->_emptyRec, $clock->getWed());
        $this->assertEquals($this->_emptyRec, $clock->getThu());
        $this->assertEquals($this->_emptyRec, $clock->getFri());
        $this->assertEquals($this->_emptyRec, $clock->getSat());
    }

    /**
     * Run tests to ensure the output is correct for set/getDay, set/getMon, etc.
     *
     * @return void
     */
    public function testDay()
    {
        $clock = new Clock();

        $clock->setDay(0, "100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getSun());

        $clock->setDay(1, "110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getMon());

        $clock->setDay(2, "111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getTue());

        $clock->setDay(3, "111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getWed());

        $clock->setDay(4, "111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getThu());

        $clock->setDay(5, "111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getFri());

        $clock->setDay(6, "111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
        $this->assertEquals("111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getSat());

        $this->assertEquals("100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getDay(0));

        $this->assertEquals("110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getDay(1));

        $this->assertEquals("111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getDay(2));

        $this->assertEquals("111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getDay(3));

        $this->assertEquals("111110000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getDay(4));

        $this->assertEquals("111111000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getDay(5));

        $this->assertEquals("111111100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", $clock->getDay(6));

        $clock->resetClock();

        $this->assertEquals($this->_emptyRec, $clock->getSun());
        $this->assertEquals($this->_emptyRec, $clock->getMon());
        $this->assertEquals($this->_emptyRec, $clock->getTue());
        $this->assertEquals($this->_emptyRec, $clock->getWed());
        $this->assertEquals($this->_emptyRec, $clock->getThu());
        $this->assertEquals($this->_emptyRec, $clock->getFri());
        $this->assertEquals($this->_emptyRec, $clock->getSat());
    }

    /**
     * Run tests to ensure the output is correct for set/getDayOffset
     *
     * @return void
     */
    public function testOffset()
    {
        $clock = new Clock();

        $clock->setDayOffset(0, 38, 1);
        $this->assertEquals(1, $clock->getDayOffset(0, 38));

        $clock->setDayOffset(1, 93, 1);
        $this->assertEquals(1, $clock->getDayOffset(1, 93));

        $clock->setDayOffset(2, 61, 1);
        $this->assertEquals(1, $clock->getDayOffset(2, 61));

        $clock->setDayOffset(3, 11, 1);
        $this->assertEquals(1, $clock->getDayOffset(3, 11));

        $clock->setDayOffset(4, 87, 1);
        $this->assertEquals(1, $clock->getDayOffset(4, 87));

        $clock->setDayOffset(5, 74, 1);
        $this->assertEquals(1, $clock->getDayOffset(5, 74));

        $clock->setDayOffset(6, 52, 1);
        $this->assertEquals(1, $clock->getDayOffset(6, 52));
    }

    /**
     * Run tests to ensure the output is correct for set/getStatus
     *
     * @return void
     */
    public function testStatus()
    {
        $clock = new Clock();

        $clock->setStatus(true);
        $this->assertTrue($clock->getStatus());
    }

    /**
     * Run tests to ensure the output is correct for set/getLastClock
     *
     * @return void
     */
    public function testLastClock()
    {
        $clock = new Clock();

        $lastClock = new \DateTime("4:30 pm", new \DateTimeZone("UTC"));
        $clock->setLastClock($lastClock);
        $this->assertEquals($lastClock->getTimestamp(), $clock->getLastClock()->getTimestamp());
    }
}
