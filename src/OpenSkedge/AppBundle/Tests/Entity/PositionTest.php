<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\Position;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/Position
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class PositionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Run a test to ensure instantiation of Position objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $position = new Position();

        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Position', $position);
    }

    /**
     * Run tests to ensure the output is correct for set/getName
     *
     * @return void
     */
    public function testName()
    {
        $position = new Position();

        $position->setName("Test Position");
        $this->assertEquals("Test Position", $position->getName());
    }

    /**
     * Run tests to ensure the output is correct for set/getDescription
     *
     * @return void
     */
    public function testDescription()
    {
        $position = new Position();

        $position->setDescription("DAS IST EIN TEST POSITION");
        $this->assertEquals("DAS IST EIN TEST POSITION", $position->getDescription());
    }

    /**
     * Run tests to ensure the output is correct for set/getArea
     *
     * @return void
     */
    public function testArea()
    {
        $position = new Position();
        $area = $this->getMock('\OpenSkedge\AppBundle\Entity\Area');

        $position->setArea($area);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Area', $position->getArea());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getSchedule(s)
     *
     * @return void
     */
    public function testSchedule()
    {
        $testSchedule = $this->getMock('\OpenSkedge\AppBundle\Entity\Schedule');

        $position = new Position();
        $position->addSchedule($testSchedule);

        $schedules = $position->getSchedules();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Schedule', $schedules[0]);

        $position->removeSchedule($testSchedule);
        $this->assertTrue($position->getSchedules()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getLateShift(s)
     *
     * @return void
     */
    public function testLateShift()
    {
        $lateShift = $this->getMock('\OpenSkedge\AppBundle\Entity\LateShift');

        $position = new Position();
        $position->addLateShift($lateShift);

        $lateShifts = $position->getLateShifts();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\LateShift', $lateShifts[0]);

        $position->removeLateShift($lateShift);
        $this->assertTrue($position->getLateShifts()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getShift(s)
     *
     * @return void
     */
    public function testShift()
    {
        $shift = $this->getMock('\OpenSkedge\AppBundle\Entity\Shift');

        $position = new Position();
        $position->addShift($shift);

        $shifts = $position->getShifts();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Shift', $shifts[0]);

        $position->removeShift($shift);
        $this->assertTrue($position->getShifts()->isEmpty());
    }
}
