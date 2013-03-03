<?php

namespace OpenSkedge\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClockPruneCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this->setName("clock:prune")
             ->setDescription('Prunes timeclock data from before the specified number of weeks back.')
             ->addArgument('[weeks to keep]', InputArgument::REQUIRED, 'number of weeks back to keep')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $weeks = sprintf("-%s weeks", $input->getArgument('[weeks to keep]'));
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $currentWeek = $this->getFirstDayOfWeek(new \DateTime("now"));
        $threshold = $currentWeek->modify($weeks);

        $clocksToBePruned = $em->createQuery('SELECT ac FROM OpenSkedgeBundle:ArchivedClock ac
                                                WHERE ac.week < :threshold')
                                 ->setParameter('threshold', $threshold)
                                 ->getResult();

        if(count($clocksToBePruned) < 1) {
            $output->writeln("Nothing to be pruned. Exiting.");
            return;
        }

        $dialog = $this->getHelperSet()->get('dialog');

        if (!$dialog->askConfirmation($output,
            '<question>Continue with this action? It will purge '.count($clocksToBePruned).' database entries!</question>', false)) {
            return;
        }

        foreach ($clocksToBePruned as $clock) {
            $output->writeln("Pruning timeclock data for week of ".$clock->getWeek()->format('Y-M-d'));
            $em->remove($clock);
            $em->flush();
        }
    }

    /**
     * @param \DateTime $date A given date
     * @return \DateTime
     */
    private function getFirstDayOfWeek(\DateTime $date)
    {
        $day = $this->getContainer()->getParameter('week_start_day_clock');
        $firstDay = idate('w', strtotime($day));
        $offset = 7 - $firstDay;
        $ret = clone $date;
        $ret->modify(-(($date->format('w') + $offset) % 7) . 'days');
        $ret->modify('midnight');
        return $ret;
    }
}
