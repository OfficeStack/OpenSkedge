<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\Shift;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/Shift
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ShiftTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Run a test to ensure instantiation of Shift objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $sh = new Shift();

        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Shift', $sh);
        $this->assertEquals('pending', $sh->getStatus());
        $this->assertInstanceOf('\DateTime', $sh->getCreationTime());
    }

    /**
     * Run tests to ensure the output is correct for set/getNotes
     *
     * @return void
     */
    public function testNotes()
    {
        $sh = new Shift();

        $sh->setNotes("Some notes and stuff!");
        $this->assertEquals("Some notes and stuff!", $sh->getNotes());
    }

    /**
     * Run tests to ensure the output is correct for set/getUser
     *
     * @return void
     */
    public function testUser()
    {
        $user = $this->getMock('\OpenSkedge\AppBundle\Entity\User');

        $sh = new Shift();

        $sh->setUser($user);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\User', $sh->getUser());
    }

    /**
     * Run tests to ensure the output is correct for set/getPickedUpBy
     *
     * @return void
     */
    public function testPickedUpBy()
    {
        $user = $this->getMock('\OpenSkedge\AppBundle\Entity\User');

        $sh = new Shift();

        $sh->setPickedUpBy($user);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\User', $sh->getPickedUpBy());
    }

    /**
     * Run tests to ensure the output is correct for set/getPosition
     *
     * @return void
     */
    public function testPosition()
    {
        $position = $this->getMock('\OpenSkedge\AppBundle\Entity\Position');

        $sh = new Shift();

        $sh->setPosition($position);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Position', $sh->getPosition());
    }

    /**
     * Run tests to ensure the output is correct for set/getSchedule
     *
     * @return void
     */
    public function testSchedule()
    {
        $schedule = $this->getMock('\OpenSkedge\AppBundle\Entity\Schedule');

        $sh = new Shift();

        $sh->setSchedule($schedule);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Schedule', $sh->getSchedule());
    }

    /**
     * Run tests to ensure the output is correct for set/getSchedulePeriod
     *
     * @return void
     */
    public function testSchedulePeriod()
    {
        $schedulePeriod = $this->getMock('\OpenSkedge\AppBundle\Entity\SchedulePeriod');

        $sh = new Shift();

        $sh->setSchedulePeriod($schedulePeriod);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\SchedulePeriod', $sh->getSchedulePeriod());
    }
}
