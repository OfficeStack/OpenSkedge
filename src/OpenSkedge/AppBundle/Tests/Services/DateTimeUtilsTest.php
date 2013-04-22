<?php

namespace OpenSkedge\AppBundle\Tests\Services;

use OpenSkedge\AppBundle\Services\DateTimeUtils;

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
class DateTimeUtilsTest extends \PHPUnit_Framework_TestCase
{
    /*
     * @var DateTimeUtils
     */
    private $_dtUtils;

    /**
     * Setup a DateTimeUtils object for the tests with various mocks.
     *
     * @return void
     */
    public function setUp()
    {
        $appSettings = $this->getMock('\OpenSkedge\AppBundle\Entity\Settings');
        $appSettings->expects($this->any())->method('getWeekStartDay')->will($this->returnValue('sunday'));
        $appSettings->expects($this->any())->method('getWeekStartDayClock')->will($this->returnValue('monday'));

        $em = $this->getMock('\Doctrine\Common\Persistence\ObjectManager');
        $appSettingsService = $this->getMock('\OpenSkedge\AppBundle\Services\AppSettingsService', array(), array($em));
        $appSettingsService->expects($this->any())->method('getAppSettings')->will($this->returnValue($appSettings));

        $this->_dtUtils = new DateTimeUtils($appSettingsService);
    }

    /**
     * Run a test to ensure instantiation of DateTimeUtils objects is working.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf('\OpenSkedge\AppBundle\Services\DateTimeUtils', $this->_dtUtils);
    }

    /**
     * Run tests to ensure the output is correct for a variety of time string formats.
     *
     * @return void
     */
    public function testTimeStrToDateTime()
    {
        $midnight = new \DateTime("midnight today", new \DateTimeZone("UTC"));

        $test1DT = clone $midnight;
        $test1DT->setTime(4, 0, 0);
        $test1Result = $this->_dtUtils->timeStrToDateTime("4:00am");
        $this->assertEquals($test1DT->getTimestamp(), $test1Result->getTimestamp());

        $test2DT = clone $midnight;
        $test2DT->setTime(23, 0, 0);
        $test2Result = $this->_dtUtils->timeStrToDateTime("23:00");
        $this->assertEquals($test2DT->getTimestamp(), $test2Result->getTimestamp());

        $test3DT = clone $midnight;
        $test3DT->setTime(11, 27, 33);
        $test3Result = $this->_dtUtils->timeStrToDateTime("11:27:33 am");
        $this->assertEquals($test3DT->getTimestamp(), $test3Result->getTimestamp());

        $test4DT = clone $midnight;
        $test4DT->setTime(15, 32, 0);
        $test4Result = $this->_dtUtils->timeStrToDateTime("03:32 pm");
        $this->assertEquals($test4DT->getTimestamp(), $test4Result->getTimestamp());
    }

    /**
     * Run tests to ensure the the correct 15 min index is correct.
     *
     * @return void
     */
    public function testGetIndexFromTime()
    {
        $test1Ind = 5;
        $test1Time = "1:15am";
        $this->assertEquals($test1Ind, $this->_dtUtils->getIndexFromTime($test1Time));

        $test2Ind = 95;
        $test2Time = "23:45:00";
        $this->assertEquals($test2Ind, $this->_dtUtils->getIndexFromTime($test2Time));

        $test3Ind = 35;
        $test3Time = new \DateTime("8:47", new \DateTimeZone("UTC"));
        $this->assertEquals($test3Ind, $this->_dtUtils->getIndexFromTime($test3Time));

    }

    /**
     * Ensure method throws correct exception on blank input.
     *
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testGetIndexFromTimeInvalidArgumentExceptionWithBlank()
    {
        $this->assertEquals(34, $this->_dtUtils->getIndexFromTime(""));
    }

    /**
     * Ensure method throws correct exception on numeric input.
     *
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testGetIndexFromTimeInvalidArgumentExceptionWithNumeric()
    {
        $this->assertEquals(12, $this->_dtUtils->getIndexFromTime(17.3));
    }

    /**
     * Ensure the correct date for the beginning of the DateTime's week is returned.
     *
     * @return void
     */
    public function testGetFirstDayOfWeek()
    {
        // Same Sun-Sat week, same month
        $test1DT = new \DateTime("April 11th, 2013", new \DateTimeZone("UTC"));
        $test1ExpResult = new \DateTime("April 7th, 2013", new \DateTimeZone("UTC"));
        $test1ExpResultClock = new \DateTime("April 8th, 2013", new \DateTimeZone("UTC"));
        $this->assertEquals($test1ExpResult->getTimestamp(), $this->_dtUtils->getFirstDayOfWeek($test1DT, false)->getTimestamp());
        $this->assertEquals($test1ExpResultClock->getTimestamp(), $this->_dtUtils->getFirstDayOfWeek($test1DT, true)->getTimestamp());

        // Same Sun-Sat week, different Months
        $test2DT = new \DateTime("March 1st, 2013", new \DateTimeZone("UTC"));
        $test2ExpResult = new \DateTime("February 24th, 2013", new \DateTimeZone("UTC"));
        $test2ExpResultClock = new \DateTime("February 25th, 2013", new \DateTimeZone("UTC"));
        $this->assertEquals($test2ExpResult->getTimestamp(), $this->_dtUtils->getFirstDayOfWeek($test2DT, false)->getTimestamp());
        $this->assertEquals($test2ExpResultClock->getTimestamp(), $this->_dtUtils->getFirstDayOfWeek($test2DT, true)->getTimestamp());

        // Week start and time clock week start in two different Sun-Sat weeks
        $test3DT = new \DateTime("Jan 20, 2013", new \DateTimeZone("UTC"));
        $test3ExpResult = new \DateTime("Jan 20, 2013", new \DateTimeZone("UTC"));
        $test3ExpResultClock = new \DateTime("Jan 14, 2013", new \DateTimeZone("UTC"));
        $this->assertEquals($test3ExpResult->getTimestamp(), $this->_dtUtils->getFirstDayOfWeek($test3DT, false)->getTimestamp());
        $this->assertEquals($test3ExpResultClock->getTimestamp(), $this->_dtUtils->getFirstDayOfWeek($test3DT, true)->getTimestamp());
    }
}
