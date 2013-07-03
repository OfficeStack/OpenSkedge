<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\LateShift;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/LateShift
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class LateShiftTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Run a test to ensure instantiation of LateShift objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $ls = new LateShift();

        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\LateShift', $ls);
        $this->assertEquals('Unknown', $ls->getStatus());
        $this->assertEquals(null, $ls->getArrivalTime());
        $this->assertInstanceOf('\DateTime', $ls->getCreationTime());
    }

    /**
     * Run tests to ensure the output is correct for set/getNotes
     *
     * @return void
     */
    public function testNotes()
    {
        $ls = new LateShift();

        $ls->setNotes("Some notes and stuff!");
        $this->assertEquals("Some notes and stuff!", $ls->getNotes());
    }

    /**
     * Run tests to ensure the output is correct for set/getUser
     *
     * @return void
     */
    public function testUser()
    {
        $user = $this->getMock('\OpenSkedge\AppBundle\Entity\User');

        $ls = new LateShift();

        $ls->setUser($user);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\User', $ls->getUser());
    }

    /**
     * Run tests to ensure the output is correct for set/getPosition
     *
     * @return void
     */
    public function testPosition()
    {
        $position = $this->getMock('\OpenSkedge\AppBundle\Entity\Position');

        $ls = new LateShift();

        $ls->setPosition($position);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Position', $ls->getPosition());
    }

    /**
     * Run tests to ensure the output is correct for set/getSchedule
     *
     * @return void
     */
    public function testSchedule()
    {
        $schedule = $this->getMock('\OpenSkedge\AppBundle\Entity\Schedule');

        $ls = new LateShift();

        $ls->setSchedule($schedule);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Schedule', $ls->getSchedule());
    }

    /**
     * Run tests to ensure the output is correct for set/getSchedulePeriod
     *
     * @return void
     */
    public function testSchedulePeriod()
    {
        $schedulePeriod = $this->getMock('\OpenSkedge\AppBundle\Entity\SchedulePeriod');

        $ls = new LateShift();

        $ls->setSchedulePeriod($schedulePeriod);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\SchedulePeriod', $ls->getSchedulePeriod());
    }
}
