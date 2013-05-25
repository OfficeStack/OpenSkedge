<?php

namespace OpenSkedge\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * CLI command for running openskedge commands as a background worker.
 *
 * @category Command
 * @package  OpenSkedge\AppBundle\Command
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class WorkerRunCommand extends ContainerAwareCommand
{

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName("openskedge:worker:run")
             ->setDescription('Prunes timeclock data from before the specified number of weeks back.')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appSettings = $this->getContainer()->get('app_settings')->getAppSettings();
        $logger = $this->getContainer()->get('logger');

        $startPrune = true;

        // This is to be used as a worker thread. Run until SIGTERM.
        set_time_limit(0);
        $i = 1;
        while (1) {
            $now = new \DateTime();

            echo "[".date('Y-m-d H:i:s')."] Run #".$i." (Current Mem Usage: ".bcdiv(memory_get_usage(), 1048576, 2)."M / Peak Mem Usage: ".bcdiv(memory_get_peak_usage(), 1048576, 2)."M)\n";

            echo "[".date('Y-m-d H:i:s')."] Checking for Late Employees...";
            $checkLateProcess = new Process("php app/console openskedge:clock:check-late");
            $checkLateProcess->run(function ($type, $buffer) use ($logger) {
                    if (Process::ERR === $type) {
                        $logger->error($buffer);
                    } else {
                        $logger->info($buffer);
                    }
                });
            echo " done!\n";

            $pruneTrigger = $appSettings->getWeekStartDayClock();

            if ($now->format('l') === ucfirst($pruneTrigger) and $startPrune) {
                echo "[".date('Y-m-d H:i:s')."] Launching Prune Process\n";
                $pruneProcess = new Process("php app/console openskedge:clock:prune --no-interaction");
                $pruneProcess->run(function ($type, $buffer) use ($logger) {
                    if (Process::ERR === $type) {
                        $logger->error($buffer);
                    } else {
                        $logger->info($buffer);
                    }
                });
            } else if ($now->format('l') !== ucfirst($pruneTrigger)) {
                $startPrune = false;
            }

            echo "[".date('Y-m-d H:i:s')."] Dispatching spooled emails...";
            $swiftmailerSpoolSend = new Process("php app/console --no-interaction swiftmailer:spool:send");
            $swiftmailerSpoolSend->run(function ($type, $buffer) use ($logger) {
                    if (Process::ERR === $type) {
                        $logger->error($buffer);
                    } else {
                        $logger->info($buffer);
                    }
                });
            echo " done!\n";

            echo "[".date('Y-m-d H:i:s')."] Run #".$i." completed. \n";

            $i++;

            // Wait 10 minutes before running commands again.
            sleep(600);
        }
    }
}
