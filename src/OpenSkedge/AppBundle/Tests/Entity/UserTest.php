<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\User;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/User
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Run a test to ensure instantiation of User objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $user = new User();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\User', $user);
    }

    /**
     * Run tests to ensure the output is correct for set/getUsername
     *
     * @return void
     */
    public function testUsername()
    {
        $user = new User();
        $user->setUsername("testuser");
        $this->assertEquals("testuser", $user->getUsername());
    }

    /**
     * Run tests to ensure the output is correct for getSalt
     *
     * @return void
     */
    public function testSalt()
    {
        $user = new User();
        $salt = $user->getSalt();
        $this->assertInternalType('string', $salt);
        $this->assertFalse(empty($salt));
        $user->setSalt("1234567890qwertyuiop");
        $this->assertEquals("1234567890qwertyuiop", $user->getSalt());
    }

    /**
     * Run tests to ensure the output is correct for set/getPassword
     *
     * @return void
     */
    public function testPassword()
    {
        $user = new User();
        $user->setPassword("password");
        $this->assertEquals("password", $user->getPassword());
    }

    /**
     * Run tests to ensure the output is correct for set/getName
     *
     * @return void
     */
    public function testName()
    {
        $user = new User();
        $user->setName("Test User");
        $this->assertEquals("Test User", $user->getName());
    }

    /**
     * Run tests to ensure the output is correct for set/getWorkphone
     *
     * @return void
     */
    public function testWorkphone()
    {
        $user = new User();
        $user->setWorkphone("(232) 231-1232");
        $this->assertEquals("(232) 231-1232", $user->getWorkphone());
    }

    /**
     * Run tests to ensure the output is correct for set/getHomephone
     *
     * @return void
     */
    public function testHomephone()
    {
        $user = new User();
        $user->setHomephone("(232) 231-1232");
        $this->assertEquals("(232) 231-1232", $user->getHomephone());
    }

    /**
     * Run tests to ensure the output is correct for set/getLocation
     *
     * @return void
     */
    public function testLocation()
    {
        $user = new User();
        $user->setLocation("Testland, Testasee");
        $this->assertEquals("Testland, Testasee", $user->getLocation());
    }

    /**
     * Run tests to ensure the output is correct for set/getEmail
     *
     * @return void
     */
    public function testEmail()
    {
        $user = new User();
        $user->setEmail("testuser@example.com");
        $this->assertEquals("testuser@example.com", $user->getEmail());
    }

    /**
     * Run tests to ensure the output is correct for set/getMin
     *
     * @return void
     */
    public function testMin()
    {
        $user = new User();
        $user->setMin(12);
        $this->assertEquals(12, $user->getMin());
    }

    /**
     * Run tests to ensure the output is correct for set/getMax
     *
     * @return void
     */
    public function testMax()
    {
        $user = new User();
        $user->setMax(28);
        $this->assertEquals(28, $user->getMax());
    }

    /**
     * Run tests to ensure the output is correct for set/getHours
     *
     * @return void
     */
    public function testHours()
    {
        $user = new User();
        $user->setHours(20);
        $this->assertEquals(20, $user->getHours());
    }

    /**
     * Run tests to ensure the output is correct for set/getNotes
     *
     * @return void
     */
    public function testNotes()
    {
        $user = new User();

        $user->setNotes("Some notes and stuff!");
        $this->assertEquals("Some notes and stuff!", $user->getNotes());
    }

    /**
     * Run tests to ensure the output is correct for set/getSupnotes
     *
     * @return void
     */
    public function testSupNotes()
    {
        $user = new User();

        $user->setSupnotes("Some notes and stuff!");
        $this->assertEquals("Some notes and stuff!", $user->getSupnotes());
    }

    /**
     * Run tests to ensure the output is correct for set/getColor
     *
     * @return void
     */
    public function testColor()
    {
        $user = new User();

        $user->setColor("#112233");
        $this->assertEquals("#112233", $user->getColor());
    }

    /**
     * Run tests to ensure the output is correct for equals()
     *
     * @return void
     */
    public function testEquals()
    {
        $user1 = new User();
        $user1->setPassword("password");
        $user1->setSalt("ramenBroth");
        $user1->setUsername("username");

        $user2 = new User();
        $user2->setPassword("password");
        $user2->setSalt("ramenBroth");
        $user2->setUsername("username");

        $this->assertTrue($user1->equals($user2));
    }

    /**
     * Run tests to ensure the output is correct for isEnabled
     *
     * @return void
     */
    public function testEnables()
    {
        $user = new User();

        $this->assertFalse($user->disable()->isEnabled());
        $this->assertTrue($user->enable()->isEnabled());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getSchedule(s)
     *
     * @return void
     */
    public function testSchedule()
    {
        $testSchedule = $this->getMock('\OpenSkedge\AppBundle\Entity\Schedule');

        $user = new User();
        $user->addSchedule($testSchedule);

        $schedules = $user->getSchedules();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Schedule', $schedules[0]);

        $user->removeSchedule($testSchedule);
        $this->assertTrue($user->getSchedules()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getSchedule(s)
     *
     * @return void
     */
    public function testAvailabilitySchedule()
    {
        $testAvailSchedule = $this->getMock('\OpenSkedge\AppBundle\Entity\AvailabilitySchedule');

        $user = new User();
        $user->addAvailabilitySchedule($testAvailSchedule);

        $availSchedules = $user->getAvailabilitySchedules();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\AvailabilitySchedule', $availSchedules[0]);

        $user->removeAvailabilitySchedule($testAvailSchedule);
        $this->assertTrue($user->getAvailabilitySchedules()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for set/getClock
     *
     * @return void
     */
    public function testClock()
    {
        $clock = $this->getMock('\OpenSkedge\AppBundle\Entity\Clock');

        $user = new User();

        $user->setClock($clock);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Clock', $user->getClock());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getSupervisor(s)
     *
     * @return void
     */
    public function testSupervisors()
    {
        $supervisor = $this->getMock('\OpenSkedge\AppBundle\Entity\User');

        $user = new User();
        $user->addSupervisor($supervisor);

        $supervisors = $user->getSupervisors();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\User', $supervisors[0]);

        $user->removeSupervisor($supervisor);
        $this->assertTrue($user->getSupervisors()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getSupervisor(s)
     *
     * @return void
     */
    public function testEmployees()
    {
        $employee = $this->getMock('\OpenSkedge\AppBundle\Entity\User');

        $user = new User();
        $user->addEmployee($employee);

        $employees = $user->getEmployees();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\User', $employees[0]);

        $user->removeEmployee($employee);
        $this->assertTrue($user->getEmployees()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for set/getGroup
     *
     * @return void
     */
    public function testGroup()
    {
        $group = $this->getMock('\OpenSkedge\AppBundle\Entity\Group');

        $user = new User();

        $user->setGroup($group);
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Group', $user->getGroup());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getArchivedClock(s)
     *
     * @return void
     */
    public function testArchivedClocks()
    {
        $archivedClock = $this->getMock('\OpenSkedge\AppBundle\Entity\ArchivedClock');

        $user = new User();
        $user->addArchivedClock($archivedClock);

        $archivedClocks = $user->getArchivedClocks();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\ArchivedClock', $archivedClocks[0]);

        $user->removeArchivedClock($archivedClock);
        $this->assertTrue($user->getArchivedClocks()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getLateShift(s)
     *
     * @return void
     */
    public function testLateShift()
    {
        $lateShift = $this->getMock('\OpenSkedge\AppBundle\Entity\LateShift');

        $user = new User();
        $user->addLateShift($lateShift);

        $lateShifts = $user->getLateShifts();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\LateShift', $lateShifts[0]);

        $user->removeLateShift($lateShift);
        $this->assertTrue($user->getLateShifts()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getShift(s)
     *
     * @return void
     */
    public function testShift()
    {
        $shift = $this->getMock('\OpenSkedge\AppBundle\Entity\Shift');

        $user = new User();
        $user->addShift($shift);

        $shifts = $user->getShifts();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Shift', $shifts[0]);

        $user->removeShift($shift);
        $this->assertTrue($user->getShifts()->isEmpty());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getPickedUpShifts(s)
     *
     * @return void
     */
    public function testPickedUpShifts()
    {
        $shift = $this->getMock('\OpenSkedge\AppBundle\Entity\Shift');

        $user = new User();
        $user->addPickedUpShift($shift);

        $shifts = $user->getPickedUpShifts();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Shift', $shifts[0]);

        $user->removePickedUpShift($shift);
        $this->assertTrue($user->getPickedUpShifts()->isEmpty());
    }
}
