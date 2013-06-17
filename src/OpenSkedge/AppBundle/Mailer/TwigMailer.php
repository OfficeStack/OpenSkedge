<?php
// Derived from FOSUserBundle
namespace OpenSkedge\AppBundle\Mailer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenSkedge\AppBundle\Entity\AvailabilitySchedule;
use OpenSkedge\AppBundle\Entity\Schedule;
use OpenSkedge\AppBundle\Entity\Shift;
use OpenSkedge\AppBundle\Services\AppSettingsService;

class TwigMailer implements MailerInterface
{
    protected $mailer;
    protected $twig;
    protected $logger;
    protected $appSettingsService;
    protected $parameters;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, LoggerInterface $logger, AppSettingsService $appSettingsService, array $parameters)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->appSettingsService = $appSettingsService;
        $this->parameters = $parameters;
    }

    public function notifyUserScheduleChange(Schedule $schedule)
    {
        $user = $schedule->getUser();
        $context = array(
            'user' => $user,
            'schedule' => $schedule,
        );
        $this->dispatchMessage('OpenSkedgeBundle:Mailer:schedulechange.txt.twig', $context,
                                $this->parameters['senderEmail'], $user->getEmail());
    }
    public function notifyAvailabilitySchedulePost(AvailabilitySchedule $schedule)
    {
        $user = $schedule->getUser();
        $supervisors = $user->getSupervisors();
        foreach($supervisors as $supervisor) {
            $context = array(
                'user' => $user,
                'schedulePeriod' => $schedule->getSchedulePeriod(),
                'supervisor' => $supervisor,
            );
            $this->dispatchMessage('OpenSkedgeBundle:Mailer:availschedulepost.txt.twig', $context,
                                    $this->parameters['senderEmail'], $supervisor->getEmail());
        }
    }
    public function notifyAvailabilityScheduleChange(AvailabilitySchedule $schedule)
    {
        $user = $schedule->getUser();
        $supervisors = $user->getSupervisors();
        foreach($supervisors as $supervisor) {
            $context = array(
                'user' => $user,
                'schedulePeriod' => $schedule->getSchedulePeriod(),
                'supervisor' => $supervisor,
            );
            $this->dispatchMessage('OpenSkedgeBundle:Mailer:availschedulechange.txt.twig',
                $context, $this->parameters['senderEmail'], $supervisor->getEmail());
        }
    }
    public function notifyUserCreation(UserInterface $user, $password)
    {
        $context = array(
            'user' => $user,
            'password' => $password,
        );
        $this->dispatchMessage('OpenSkedgeBundle:Mailer:newuser.txt.twig', $context,
                                $this->parameters['senderEmail'], $user->getEmail());
    }
    public function notifyNewSupervisor(UserInterface $user, UserInterface $newSupervisor)
    {
        $context = array(
            'user' => $user,
            'newSupervisor' => $newSupervisor,
        );
        $this->dispatchMessage('OpenSkedgeBundle:Mailer:newsupervisor.txt.twig', $context,
                                $this->parameters['senderEmail'], $user->getEmail());
    }

    public function notifyLateEmployee(UserInterface $user, Schedule $schedule)
    {
        $supervisors = $user->getSupervisors();
        foreach ($supervisors as $supervisor) {
            $context = array(
                'user' => $user,
                'supervisor' => $supervisor,
                'position' => $schedule->getPosition(),
            );
            $this->dispatchMessage('OpenSkedgeBundle:Mailer:lateemployee_sup.txt.twig',
                $context, $this->parameters['senderEmail'], $supervisor->getEmail());
        }
        $context = array(
                'user' => $user,
                'position' => $schedule->getPosition(),
            );
        $this->dispatchMessage('OpenSkedgeBundle:Mailer:lateemployee_emp.txt.twig',
            $context, $this->parameters['senderEmail'], $user->getEmail());
    }

    public function notifyShiftPosted(Shift $shift)
    {
        $user = $shift->getUser();
        foreach ($user->getSupervisors() as $supervisor) {
            $context = array(
                'shift' => $shift,
                'supervisor' => $supervisor
            );
            $this->dispatchMessage('OpenSkedgeBundle:Mailer:shiftposted_sup.txt.twig',
                $context, $this->parameters['senderEmail'], $supervisor->getEmail());
        }
        $massEmail = $appSettingsService->getAppSettings()->getMassEmail();
        if (!empty($massEmail)) {
            $context = array('shift' => $shift);
            $this->dispatchMessage('OpenSkedgeBundle:Mailer:shiftposted_mass.txt.twig',
                $context, $user->getEmail(), $appSettingsService->getAppSettings()->getMassEmail());
        }
    }

    public function notifyShiftPickedUp(Shift $shift)
    {
        $this->dispatchMessage('OpenSkedgeBundle:Mailer:shiftpickedup.txt.twig',
            array('shift' => $shift), $this->parameters['senderEmail'], $shift->getUser()->getEmail());
    }

    public function notifyShiftDenied(Shift $shift)
    {
        $this->dispatchMessage('OpenSkedgeBundle:Mailer:shiftdenied.txt.twig',
            array('shift' => $shift), $this->parameters['senderEmail'], $shift->getUser()->getEmail());
        if ($shift->getPickedUpBy instanceof UserInterface) {
            $this->dispatchMessage('OpenSkedgeBundle:Mailer:shiftdenied_pickedup.txt.twig',
            array('shift' => $shift), $this->parameters['senderEmail'], $shift->getPickedUpBy()->getEmail());
        }
    }

    /**
     * Render the email, use the first line as the subject, and the rest as the body
     *
     * @param string $templateName  Name of the Twig template to use to render the email.
     * @param string $context       Variables and their values to inject into the rendered email.
     * @param string $fromEmail     The originating email address
     * @param string $toEmail       The destination email address
     *
     * @return void
     */
    protected function dispatchMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $template = $this->twig->loadTemplate($templateName);
        $subject  = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setReturnPath($fromEmail)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                    ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        if(!$this->mailer->send($message,$failures)) {
            $this->logger->error('An email failed to send: '.$failures);
        }
    }
}
