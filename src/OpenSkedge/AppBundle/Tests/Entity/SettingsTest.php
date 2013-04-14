<?php

namespace OpenSkedge\AppBundle\Tests\Entity;

use OpenSkedge\AppBundle\Entity\Settings;

/**
 * Runs tests on OpenSkedge/AppBundle/Entity/Settings
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Entity
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class SettingsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Run a test to ensure instantiation of Settings objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $settings = new Settings();

        $this->assertInstanceOf('\OpenSkedge\AppBundle\Entity\Settings', $settings);
    }

    /**
     * Run tests to ensure the output is correct for set/getBrandName
     *
     * @return void
     */
    public function testBrandName()
    {
        $settings = new Settings();

        $settings->setBrandName("Test Area");
        $this->assertEquals("Test Area", $settings->getBrandName());
    }

    /**
     * Run tests to ensure the output is correct for set/getPruneAfter
     *
     * @return void
     */
    public function testPruneAfter()
    {
        $settings = new Settings();

        $settings->setPruneAfter(7);
        $this->assertEquals(7, $settings->getPruneAfter());
    }

    /**
     * Run tests to ensure the output is correct for set/getWeekStartDay
     *
     * @return void
     */
    public function testWeekStartDay()
    {
        $settings = new Settings();

        $settings->setWeekStartDay("sunday");
        $this->assertEquals("sunday", $settings->getWeekStartDay());
    }

    /**
     * Run tests to ensure the output is correct for set/getWeekStartDayClock
     *
     * @return void
     */
    public function testWeekStartDayClock()
    {
        $settings = new Settings();

        $settings->setWeekStartDayClock("monday");
        $this->assertEquals("monday", $settings->getWeekStartDayClock());
    }

    /**
     * Run tests to ensure the output is correct for set/getDefaultTimeResolution
     *
     * @return void
     */
    public function testDefaultTimeResolution()
    {
        $settings = new Settings();

        $settings->setDefaultTimeResolution("15 mins");
        $this->assertEquals("15 mins", $settings->getDefaultTimeResolution());
    }

    /**
     * Run tests to ensure the output is correct for set/getStartHour
     *
     * @return void
     */
    public function testStartHour()
    {
        $settings = new Settings();

        $settings->setStartHour("07:00 am");
        $this->assertEquals("7:00 AM", $settings->getStartHour());

        $settings->setStartHour("7:00:23");
        $this->assertEquals("7:00 AM", $settings->getStartHour());
    }

    /**
     * Run tests to ensure the output is correct for set/getStartHour
     *
     * @return void
     */
    public function testEndHour()
    {
        $settings = new Settings();

        $settings->setStartHour("12:00:00 pm");
        $this->assertEquals("12:00 PM", $settings->getStartHour());

        $settings->setStartHour("12:00:38 pm");
        $this->assertEquals("12:00 PM", $settings->getStartHour());
    }

}
