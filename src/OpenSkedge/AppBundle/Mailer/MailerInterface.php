<?php
// Derived from FOSUserBundle
namespace OpenSkedge\AppBundle\Mailer;

use Symfony\Component\Security\Core\User\UserInterface;
use OpenSkedge\AppBundle\Entity\AvailabilitySchedule;
use OpenSkedge\AppBundle\Entity\Schedule;

interface MailerInterface
{
    public function notifyUserScheduleChange(Schedule $schedule);
    public function notifyAvailabilitySchedulePost(AvailabilitySchedule $schedule);
    public function notifyAvailabilityScheduleChange(AvailabilitySchedule $schedule);
    public function notifyUserCreation(UserInterface $user);
    public function notifyNewSupervisor(UserInterface $user);
    public function notifyLateEmployee(UserInterface $user, Schedule $schedule);

    function dispatchMessage($template, $context, $fromEmail, $toEmail);
}
