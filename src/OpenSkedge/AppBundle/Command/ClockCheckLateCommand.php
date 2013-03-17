<?php

namespace OpenSkedge\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use OpenSkedge\AppBundle\Entity\Schedule;
use OpenSkedge\AppBundle\Entity\User;

class ClockCheckLateCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this->setName("clock:check-late")
            ->setDescription('Check for users who have yet to clock in for a shift and notify each of their supervisors.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $mailer = $this->getContainer()->get('notify_mailer');

        $results = $em->createQuery('SELECT sp FROM OpenSkedgeBundle:SchedulePeriod sp
                                    WHERE (sp.startTime <= CURRENT_TIMESTAMP()
                                    AND sp.endTime >= CURRENT_TIMESTAMP())
                                    ORDER BY sp.endTime DESC')
            ->getResult();

        $schedules = array();
        foreach ($results as $result) {
            $schedules[] = $result->getSchedules();
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
                if (!$user) {
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
                    $curIndex = (int)((($curTime->getTimestamp() - $midnight->getTimestamp()) / 60) / 15);

                    $dayNum = $curTime->format('w');

                    foreach ($schedules as $schedule) {
                        for ($timesect = 0; $timesect < 96; $timesect++) {
                            for ($day = 0; $day < 7; $day++) {
                                if ($schedule->getDayOffset($dayNum, $curIndex-1) == '1' && $user->getClock()->getDayOffset($dayNum, $curIndex-1) == '0') {
                                    $mailer->notifyLateEmployee($user, $schedule);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
