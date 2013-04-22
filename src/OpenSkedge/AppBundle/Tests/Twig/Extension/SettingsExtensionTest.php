<?php

namespace OpenSkedge\AppBundle\Tests\Twig\Extension;

use \OpenSkedge\AppBundle\Twig\Extension\SettingsExtension;

/**
 * Runs tests on OpenSkedge/AppBundle/Services/DateTimeUtils
 *
 * @category Tests
 * @package  OpenSkedge\AppBundle\Tests\Services
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class SettingsExtensionTest extends \PHPUnit_Framework_TestCase
{
    /*
     * @var SettingsExtension
     */
    protected $settingsExt;

    /**
     * Setup a DateTimeUtils object for the tests with various mocks.
     *
     * @return void
     */
    public function setUp()
    {
        $appSettings = $this->getMock('\OpenSkedge\AppBundle\Entity\Settings');
        $appSettings->expects($this->any())->method('getStartHour')->will($this->returnValue('07:00 AM'));
        $appSettings->expects($this->any())->method('getEndHour')->will($this->returnValue('10:00 PM'));
        $appSettings->expects($this->any())->method('getBrandName')->will($this->returnValue('OpenSkedge'));
        $appSettings->expects($this->any())->method('getDefaultTimeResolution')->will($this->returnValue('15 mins'));

        $em = $this->getMock('\Doctrine\Common\Persistence\ObjectManager');
        $appSettingsService = $this->getMock('\OpenSkedge\AppBundle\Services\AppSettingsService', array(), array($em));
        $appSettingsService->expects($this->any())->method('getAppSettings')->will($this->returnValue($appSettings));

        $this->settingsExt = new SettingsExtension($appSettingsService);
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Twig\Extension\SettingsExtension', $this->settingsExt);
    }

    public function testFunctions()
    {
        $funcs = $this->settingsExt->getFunctions();

        foreach($funcs as $func) {
            $this->assertInstanceOf('\Twig_Function_Method', $func);
        }
    }

    public function testBrandName()
    {
        $this->assertEquals('OpenSkedge', $this->settingsExt->getBrandName());
    }

    public function testStartHour()
    {
        $this->assertEquals('07:00 AM', $this->settingsExt->getStartHour());
    }

    public function testEndHour()
    {
        $this->assertEquals('10:00 PM', $this->settingsExt->getEndHour());
    }

    public function testTimeResolution()
    {
        $this->assertEquals('15 mins', $this->settingsExt->getTimeResolution());
    }

    public function testName()
    {
        $this->assertEquals('app_settings', $this->settingsExt->getName());
    }
}
