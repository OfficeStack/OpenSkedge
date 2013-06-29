<?php

namespace OpenSkedge\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * CLI command for clocking out all users.
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ClockOutCommand extends ContainerAwareCommand
{

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName("openskedge:clock:out")
             ->setDescription('Clocks out the specified users')
             ->addArgument('UID', InputArgument::OPTIONAL, 'user ID of user to clock out')
             ->addOption('all', 'a', InputOption::VALUE_NONE, 'Clock out all clocked-in users')
             ->addOption('no-interaction', 'n', InputOption::VALUE_NONE, 'Do not ask any interactive questions.')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $appSettings = $this->getContainer()->get('app_settings')->getAppSettings();

        if (!$input->getOption('all') and !is_null($input->getArgument('UID'))) {
            $user = $em->getRepository('OpenSkedgeBundle:User')->find($input->getArgument('UID'));
            if (!$user instanceof UserInterface) {
                throw new \InvalidArgumentException("User not found!");
            }
            if ($user->getClock()->getStatus()) {
                $output->writeln("Clocking out ".$user->getName()." (UID: ".$user->getId().")");
                $this->getContainer()->get('clock_utils')->clockOut($user);
                return;
            }
            $output->writeln("User is not clocked in. Exiting.");
            return;
        } elseif ($input->getOption('all')) {
            $clocks = $em->getRepository('OpenSkedgeBundle:Clock')->findByStatus(true);

            if (count($clocks) < 1) {
                $output->writeln("No users to clock out. Exiting.");
                return;
            }

            $dialog = $this->getHelperSet()->get('dialog');

            // If we're on an interactive terminal, ask for confirmation first.
            if (!$dialog->askConfirmation($output,
                '<question>Continue with this action? It will clock out '.count($clocks).' users!</question>', false) && !$input->getOption('no-interaction')) {
                return;
            }

            // Clock 'em all out
            foreach ($clocks as $clock) {
                $user = $clock->getUser();
                $output->writeln("Clocking out ".$user->getName()." (UID: ".$user->getId().")");
                $this->getContainer()->get('clock_utils')->clockOut($user);
            }
        } else {
            throw new InvalidOptionsException('No option or arguments passed.');
        }
    }
}
