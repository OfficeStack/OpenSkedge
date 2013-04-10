<?php

namespace OpenSkedge\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use OpenSkedge\AppBundle\Entity\Schedule;
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
        $this->setName("clock:check-late")
            ->setDescription('Check for users who have yet to clock in for a shift and notify each of their supervisors.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $mailer = $this->getContainer()->get('notify_mailer');

        $schedulePeriods = $em->createQuery('SELECT sp FROM OpenSkedgeBundle:SchedulePeriod sp
                                    WHERE (sp.startTime <= CURRENT_TIMESTAMP()
                                    AND sp.endTime >= CURRENT_TIMESTAMP())
                                    ORDER BY sp.endTime DESC')
            ->getResult();

        $schedules = array();
        foreach ($schedulePeriods as $schedulePeriod) {
            $schedules[] = $schedulePeriod->getSchedules();
        }

        $users = array();
        for ($i = 0; $i < count($results); $i++) {
            foreach ($schedules[$i] as $schedule) {
                $users[] = $schedule->getUser()->getId();
            }
        }
        $uids = array_unique($users);

        foreach ($results as $schedulePeriod) {
            foreach ($uids as $uid) {
                $user = $em->getRepository('OpenSkedgeBundle:User')->find($uid);
                if (!$user instanceof User) {
                    throw new Exception('User was not found! There appears to be an orphaned schedule.');
                } else {
                    $schedules = $em->createQuery('SELECT s FROM OpenSkedgeBundle:Schedule s
                                                  WHERE (s.schedulePeriod = :sid AND s.user = :uid)
                                                  ORDER BY s.schedulePeriod ASC')
                        ->setParameter('sid', $schedulePeriod->getId())
                        ->setParameter('uid', $uid)
                        ->getResult();

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

                    /**
                     * Check the last 15 minutes to see if the user was clocked in,
                     * if they were supposed to be.
                     */
                    foreach ($schedules as $schedule) {
                        if ($schedule->getDayOffset($dayNum, $curIndex-1) == '1' && $user->getClock()->getDayOffset($dayNum, $curIndex-1) == '0') {
                            // Write the output to the console
                            $output->writeln($user->getName()." is late for their ".$schedule->getPosition()->getArea()->getName()." - ".$schedule->getPosition()->getName()." shift.");
                            // Send their supervisors an email.
                            $mailer->notifyLateEmployee($user, $schedule);
                        }
                    }
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
