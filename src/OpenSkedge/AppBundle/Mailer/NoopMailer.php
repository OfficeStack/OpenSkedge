<?php
// Derived from FOSUserBundle
namespace OpenSkedge\AppBundle\Mailer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenSkedge\AppBundle\Entity\AvailabilitySchedule;
use OpenSkedge\AppBundle\Entity\Schedule;

class NoopMailer implements MailerInterface
{
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, LoggerInterface $logger, array $parameters)
    {
    }

    public function notifyUserScheduleChange(Schedule $schedule)
    {
    }

    public function notifyAvailabilitySchedulePost(AvailabilitySchedule $schedule)
    {
    }

    public function notifyAvailabilityScheduleChange(AvailabilitySchedule $schedule)
    {
    }

    public function notifyUserCreation(UserInterface $user, $password)
    {
    }

    public function notifyNewSupervisor(UserInterface $user, UserInterface $newSupervisor)
    {
    }

    public function notifyLateEmployee(UserInterface $user, Schedule $schedule)
    {
    }

    public function notifyShiftPosted(Shift $shift)
    {
    }

    public function notifyShiftPickedUp(Shift $shift)
    {
    }

    public function notifyShiftUpdated(Shift $shift)
    {
    }

    public function notifyShiftDenied(Shift $shift)
    {
    }
}
