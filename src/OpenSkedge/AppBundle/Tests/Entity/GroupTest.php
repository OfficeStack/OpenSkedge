<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\Group;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/Group
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Run a test to ensure instatiation of Group objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $group = new Group();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Group', $group);
    }

    /**
     * Run tests to ensure the output is correct for set/getName
     *
     * @return void
     */
    public function testName()
    {
        $group = new Group();

        $group->setName("Test Group");
        $this->assertEquals("Test Group", $group->getName());
    }

    /**
     * Run tests to ensure the output is correct for set/getRole
     *
     * @return void
     */
    public function testRole()
    {
        $group = new Group();

        $group->setRole("ROLE_TEST");
        $this->assertEquals("ROLE_TEST", $group->getRole());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getPosition(s)
     *
     * @return void
     */
    public function testUser()
    {
        $testUser = $this->getMock('\OpenSkedge\AppBundle\Entity\User');

        $group = new Group();
        $group->addUser($testUser);

        $groups = $group->getUsers();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\User', $groups[0]);

        $group->removeUser($testUser);
        $this->assertTrue($group->getUsers()->isEmpty());
    }
}
