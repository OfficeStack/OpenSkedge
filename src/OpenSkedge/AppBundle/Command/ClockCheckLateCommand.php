<?php

namespace OpenSkedge\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use OpenSkedge\AppBundle\Entity\LateShift;
use OpenSkedge\AppBundle\Entity\Schedule;
use OpenSkedge\AppBundle\Entity\Shift;
use OpenSkedge\AppBundle\Entity\User;

/**
 * CLI command for checking which users are late and alerting their supervisors
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ClockCheckLateCommand extends ContainerAwareCommand
{

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName("openskedge:clock:check-late")
            ->setDescription('Check for users who have yet to clock in for a shift and notify each of their supervisors.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $mailer = $this->getContainer()->get('notify_mailer');

        $schedulePeriods = $em->getRepository('OpenSkedgeBundle:SchedulePeriod')->findCurrentSchedulePeriods();

        $schedules = array();
        foreach ($schedulePeriods as $schedulePeriod) {
            $schedules[] = $schedulePeriod->getSchedules();
        }

        $users = array();
        for ($i = 0; $i < count($schedulePeriods); $i++) {
            foreach ($schedules[$i] as $schedule) {
                $users[] = $schedule->getUser()->getId();
            }
        }
        $uids = array_unique($users);

        $midnight = new \DateTime("midnight today");
        $curTime = new \DateTime("now");

        // Determine which 15 minute segment of the day we're in currently.
        $curIndex = (int)((($curTime->getTimestamp() - $midnight->getTimestamp()) / 60) / 15);

        $dayNum = $curTime->format('w');

        // If in the first 15 mins of today, check the last 15 mins of yesterday.
        if ($curIndex == 0) {
            $curIndex = 96;
            if ($dayNum > 0) { // If after sunday, go back a day.
                $dayNum -= 1;
            } else {           // Otherwise, go back to saturday.
                $dayNum = 6;
            }
        }

        foreach ($schedulePeriods as $schedulePeriod) {
            foreach ($uids as $uid) {
                $user = $em->getRepository('OpenSkedgeBundle:User')->find($uid);
                $clock = $user->getClock();
                $clockStatus = $clock->getDayOffset($dayNum, $curIndex-1);
                if (!$user instanceof User) {
                    throw new \Exception('User was not found! There appears to be an orphaned schedule.');
                }

                // Only check if the user hasn't clocked in yet.
                if (!$clock->getStatus() && $clockStatus == '0') {
                    $late = false;

                    // Check if they've picked up a shift during the current time.
                    $pickedUpShifts = $em->createQuery('SELECT sh FROM OpenSkedgeBundle:Shift sh
                        WHERE (sh.pickedUpBy = :uid AND sh.startTime < CURRENT_TIMESTAMP() AND sh.endTime > CURRENT_TIMESTAMP() AND sh.status != \'unapproved\')')
                            ->setParameter('uid', $user->getId())
                            ->setMaxResults(1)
                            ->getResult();
                    if (count($pickedUpShifts) > 0) {
                        $pickedUpShift = $pickedUpShifts[0];
                    } else {
                        $pickedUpShift = null;
                    }

                    // If they do, they're late.
                    if ($pickedUpShift instanceof Shift) {
                        // Write the output to the console
                        $output->writeln($user->getName()." is late for a ".$pickedUpShift->getPosition()." shift they picked up from ".$pickedUpShift->getUser());
                        $late = true;
                        $lateSchedule = $pickedUpShift->getSchedule();
                    }

                    // Check if they have shift posted during the current time.
                    $postedShifts = $em->createQuery('SELECT sh FROM OpenSkedgeBundle:Shift sh
                        WHERE (sh.user = :uid AND sh.startTime < CURRENT_TIMESTAMP() AND sh.endTime > CURRENT_TIMESTAMP() AND sh.status != \'unapproved\')')
                            ->setParameter('uid', $user->getId())
                            ->setMaxResults(1)
                            ->getResult();
                    if (count($postedShifts) > 0) {
                        $postedShift = $postedShifts[0];
                    } else {
                        $postedShift = null;
                    }

                    /**
                     * If they're not already late and they have not posted their current shift,
                     * then it is time to check their position schedules.
                     */
                    if (!$late and $postedShift instanceof Shift === false) {
                        $schedules = $em->getRepository('OpenSkedgeBundle:Schedule')
                                        ->findUserSchedulesBySchedulePeriod($uid, $schedulePeriod->getId());

                        /**
                         * Check the last 15 minutes to see if the user was clocked in,
                         * if they were scheduled.
                         */
                        foreach ($schedules as $schedule) {
                            if ($schedule->getDayOffset($dayNum, $curIndex-1) == '1') {
                                // Write the output to the console
                                $output->writeln($user->getName()." is late for their ".$schedule->getPosition()." shift.");
                                $late = true;
                                $lateSchedule = $schedule;
                                break;
                            }
                        }
                    } else if ($postedShift instanceof Shift) {
                        $lateSchedule = $postedShift->getSchedule();
                    }

                    if ($late) {
                        // Send their supervisors an email.
                        $mailer->notifyLateEmployee($user, $lateSchedule);
                        /* Get a collection of LateShift entities for today from the user
                         * where the user has not clocked in for the shift yet.
                         */
                        $lateShifts = $em->getRepository('OpenSkedgeBundle:LateShift')
                                         ->findLateShiftsTodayBySchedule($schedule->getId());

                        if (count($lateShifts) > 0) {
                            $lateShift = $lateShifts[0];
                        } else {
                            $lateShift = null;
                        }

                        if (!$lateShift instanceof LateShift) {
                            // Create a LateShift entity about this event (if not already created).
                            $lateShift = new LateShift();
                            $lateShift->setUser($user);
                            $lateShift->setSchedule($schedule);
                            $lateShift->setSchedulePeriod($schedule->getSchedulePeriod());
                            $lateShift->setPosition($schedule->getPosition());
                            $em->persist($lateShift);
                        }
                    }

                    $em->flush();
                }
            }
        }

        $transport = $this->getContainer()->get('mailer')->getTransport();
        if (!$transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }

        $spool = $transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            return;
        }

        // Flush the email spool manually.
        $spool->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
    }
}
