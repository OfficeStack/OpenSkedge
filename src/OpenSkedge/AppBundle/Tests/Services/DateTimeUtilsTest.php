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
        $test1Result = $this->_dtUtils->timeStrToDateTime("4:00am", true);
        $this->assertEquals($test1DT->getTimestamp(), $test1Result->getTimestamp());

        $test3DT = clone $midnight;
        $test3DT->setTime(11, 27, 33);
        $test3Result = $this->_dtUtils->timeStrToDateTime("11:27:33 am", true);
        $this->assertEquals($test3DT->getTimestamp(), $test3Result->getTimestamp());

        $midnight = new \DateTime("midnight today");

        $test3DT = clone $midnight;
        $test3DT->setTime(23, 0, 0);
        $test3Result = $this->_dtUtils->timeStrToDateTime("23:00");
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
        $this->assertEquals($test3Ind, $this->_dtUtils->getIndexFromTime($test3Time, true));

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

    /**
     * Run tests to ensure the the correct DateTime is returned from getDateTimeFromIndex()
     *
     * @return void
     */
    public function testDateTimeFromIndex()
    {
        $time1Ind = 5;
        $time1DT = new \DateTime("midnight this sunday");
        $time1DT->setTime(1, 15, 0);
        $this->assertEquals($time1DT->getTimestamp(), $this->_dtUtils->getDateTimeFromIndex(0, $time1Ind)->getTimestamp());

        $time2Ind = 95;
        $time2DT = new \DateTime("midnight this thursday");
        $time2DT->setTime(23, 45, 0);
        $this->assertEquals($time2DT->getTimestamp(), $this->_dtUtils->getDateTimeFromIndex(4, $time2Ind)->getTimestamp());

        $time3Ind = 35;
        $time3DT = new \DateTime("midnight this saturday");
        $time3DT->setTime(8, 45, 0);
        $this->assertEquals($time3DT->getTimestamp(), $this->_dtUtils->getDateTimeFromIndex(6, $time3Ind)->getTimestamp());

        $time4Ind = 5;
        $time4DT = new \DateTime("midnight this sunday", new \DateTimeZone("UTC"));
        $time4DT->setTime(1, 15, 0);
        $this->assertEquals($time4DT->getTimestamp(), $this->_dtUtils->getDateTimeFromIndex(0, $time4Ind, true)->getTimestamp());

        $time5Ind = 95;
        $time5DT = new \DateTime("midnight this thursday", new \DateTimeZone("UTC"));
        $time5DT->setTime(23, 45, 0);
        $this->assertEquals($time5DT->getTimestamp(), $this->_dtUtils->getDateTimeFromIndex(4, $time5Ind, true)->getTimestamp());

        $time6Ind = 35;
        $time6DT = new \DateTime("midnight this saturday", new \DateTimeZone("UTC"));
        $time6DT->setTime(8, 45, 0);
        $this->assertEquals($time6DT->getTimestamp(), $this->_dtUtils->getDateTimeFromIndex(6, $time6Ind, true)->getTimestamp());

    }

    /**
     * Run tests to ensure the the correct intervals are returned from getDateTimeIntervals()
     *
     * @return void
     */
    public function testDateTimeIntervals()
    {
        $interval1Start = new \DateTime("midnight this sunday");
        $interval1End = new \DateTime("midnight this sunday");
        $interval1End->setTime(1, 30, 0);

        $interval2Start = new \DateTime("midnight this sunday");
        $interval2Start->setTime(11, 15, 0);
        $interval2End = new \DateTime("midnight this sunday");
        $interval2End->setTime(16, 45, 0);

        $interval3Start = new \DateTime("midnight this sunday");
        $interval3Start->setTime(20, 30, 0);
        $interval3End = new \DateTime("midnight this sunday");
        $interval3End->modify("+1 day");

        $testRecord = "111110000000000000000000000000000000000000000111111111111111111111000000000000000011111111111111";

        $testIntervals = array(
            array($interval1Start, $interval1End),
            array($interval2Start, $interval2End),
            array($interval3Start, $interval3End),
        );

        $genIntervals = $this->_dtUtils->getDateTimeIntervals($testRecord, 0);

        for ($i = 0; $i < count($testIntervals); $i++) {
            $this->assertEquals($testIntervals[$i][0], $genIntervals[$i][0]);
            $this->assertEquals($testIntervals[$i][1], $genIntervals[$i][1]);
        }
    }
}
