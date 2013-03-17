<?php
// Derived from FOSUserBundle
namespace OpenSkedge\AppBundle\Mailer;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenSkedge\AppBundle\Entity\AvailabilitySchedule;
use OpenSkedge\AppBundle\Entity\Schedule;
use OpenSkedge\AppBundle\Mailer\MailerInterface;

class Mailer
{
    protected $mailer;
    protected $twig;
    protected $logger;
    protected $parameters;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, Logger $logger,  array $parameters)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->parameters = $parameters;
    }

    public function notifyUserScheduleChange(Schedule $schedule)
    {
        $user = $schedule->getUser();
        $context = array(
            'user' => $user,
            'schedule' => $schedule,
            'appname' => $this->parameters['appname'],
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
                'appname' => $this->parameters['appname'],
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
                'appname' => $this->parameters['appname'],
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
            'appname' => $this->parameters['appname'],
        );
        $this->dispatchMessage('OpenSkedgeBundle:Mailer:newuser.txt.twig', $context,
                                $this->parameters['senderEmail'], $user->getEmail());
    }
    public function notifyNewSupervisor(UserInterface $user, UserInterface $newSupervisor)
    {
        $context = array(
            'user' => $user,
            'newSupervisor' => $newSupervisor,
            'appname' => $this->parameters['appname'],
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
                'appname' => $this->parameters['appname'],
            );
            $this->dispatchMessage('OpenSkedgeBundle:Mailer:lateemployee_sup.txt.twig',
                $context, $this->parameters['senderEmail'], $supervisor->getEmail());
        }
        $context = array(
                'user' => $user,
                'position' => $schedule->getPosition(),
                'appname' => $this->parameters['appname'],
            );
        $this->dispatchMessage('OpenSkedgeBundle:Mailer:lateemployee_emp.txt.twig',
                $context, $this->parameters['senderEmail'], $user->getEmail());
    }

    /**
     * Render the email, use the first line as the subject, and the rest as the body
     *
     * @param string $template
     * @param string $context
     * @param string $fromEmail
     * @param string $toEmail
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
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                    ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        if(!$this->mailer->send($message,$failures)) {
            $this->logger->err('An email failed to send: '.$failures);
        }
    }
}
