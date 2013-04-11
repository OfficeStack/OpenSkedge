<?php

namespace OpenSkedge\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI command for pruning time clock data after a number of weeks
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ClockPruneCommand extends ContainerAwareCommand
{

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName("clock:prune")
             ->setDescription('Prunes timeclock data from before the specified number of weeks back.')
             ->addArgument('threshold', InputArgument::OPTIONAL, 'number of weeks back to keep')
             ->addOption('no-interaction', 'n', InputOption::VALUE_NONE, 'Do not ask any interactive questions.')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        // If passed an argument from the command, use it.
        if (!is_null($input->getArgument('threshold'))) {
            $weeks = sprintf("-%s weeks", $input->getArgument('threshold'));
        } else { // Otherwise take the threshold from the application settings.
            $appSettings = $this->getContainer()->get('app_settings')->getAppSettings();
            $weeks = sprintf("-%s weeks", $appSettings->getPruneAfter());
        }

        $dtUtils = $this->getContainer()->get('dt_utils');

        $currentWeek = $dtUtils->getFirstDayOfWeek(new \DateTime("now"), true);
        $threshold = $currentWeek->modify($weeks);

        // Build a list of ArchivedClock entities older than the threshold date.
        $clocksToBePruned = $em->createQuery('SELECT ac FROM OpenSkedgeBundle:ArchivedClock ac
                                                WHERE ac.week < :threshold')
            ->setParameter('threshold', $threshold)
            ->getResult();

        if (count($clocksToBePruned) < 1) {
            $output->writeln("Nothing to be pruned. Exiting.");
            return;
        }

        $dialog = $this->getHelperSet()->get('dialog');

        // If we're on an interactive terminal, ask for confirmation first.
        if (!$dialog->askConfirmation($output,
            '<question>Continue with this action? It will purge '.count($clocksToBePruned).' database entries!</question>', false) && !$input->getOption('no-interaction')) {
            return;
        }

        // Purge 'em
        foreach ($clocksToBePruned as $clock) {
            $output->writeln("Pruning timeclock data for week of ".$clock->getWeek()->format('Y-M-d'));
            $em->remove($clock);
            $em->flush();
        }
    }
}
