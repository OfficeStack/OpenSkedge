<?php

namespace OpenSkedge\AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AppSettings
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAppSettings()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        // Grab the settings row from os_settings
        $appSettings = $em->getRepository('OpenSkedgeBundle:Settings')->find(1);
        if (!$appSettings) {
            throw new Exception('OpenSkedge settings could not be found')
        }

        $clockableIps = $em->createQuery('SELECT ip.ip FROM OpenSkedgeBundle:IP ip WHERE ip.canClockIn = true')
            ->getResult();
        $this->container->setParameter('allowed_clock_ips', $clockableIps);
    }
}
