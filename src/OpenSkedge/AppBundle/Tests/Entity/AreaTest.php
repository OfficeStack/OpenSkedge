<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\Area;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/Area
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class AreaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Run a test to ensure instatiation of Area objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $area = new Area();

        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Area', $area);
    }

    /**
     * Run tests to ensure the output is correct for set/getName
     *
     * @return void
     */
    public function testName()
    {
        $area = new Area();

        $area->setName("Test Area");
        $this->assertEquals("Test Area", $area->getName());
    }

    /**
     * Run tests to ensure the output is correct for set/getDescription
     *
     * @return void
     */
    public function testDescription()
    {
        $area = new Area();

        $area->setDescription("DAS IST EIN TEST AREA");
        $this->assertEquals("DAS IST EIN TEST AREA", $area->getDescription());
    }

    /**
     * Run tests to ensure the output is correct for add/remove/getPosition(s)
     *
     * @return void
     */
    public function testPosition()
    {
        $testPosition = $this->getMock('\OpenSkedge\AppBundle\Entity\Position');

        $area = new Area();
        $area->addPosition($testPosition);

        $positions = $area->getPositions();
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Position', $positions[0]);

        $area->removePosition($testPosition);
        $this->assertTrue($area->getPositions()->isEmpty());
    }
}
