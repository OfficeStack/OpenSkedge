<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\IP;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/IP
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class IPTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Run a test to ensure instantiation of IP objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $ip = new IP();

        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\IP', $ip);
    }

    /**
     * Run tests to ensure the output is correct for set/getIP
     *
     * @return void
     */
    public function testIP()
    {
        $ip = new IP();

        $ip->setIP("127.0.0.1");
        $this->assertEquals("127.0.0.1", $ip->getIP());
    }

    /**
     * Run tests to ensure the output is correct for set/getName
     *
     * @return void
     */
    public function testName()
    {
        $ip = new IP();

        $ip->setName("Localhost");
        $this->assertEquals("Localhost", $ip->getName());
    }

    /**
     * Run tests to ensure the output is correct for is/setClockEnabled
     *
     * @return void
     */
    public function testClockEnabled()
    {
        $ip = new IP();

        $ip->setClockEnabled(true);
        $this->assertTrue($ip->isClockEnabled());
    }
}
