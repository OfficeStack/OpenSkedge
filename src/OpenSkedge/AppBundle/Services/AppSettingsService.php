<?php

namespace OpenSkedge\AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AppSettingsService
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
        if (!$appSettings instanceof \OpenSkedge\AppBundle\Entity\Settings) {
            throw new Exception('OpenSkedge settings could not be found');
        }
        return $appSettings;
    }

    public function getAllowedClockIps()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $ipObjs = $em->getRepository('OpenSkedgeBundle:IP')->findBy(array('clockEnabled'=> true));
        $ips = array();
        foreach ($ipObjs as $ipObj) {
            $ips[] = $ipObj->getIp();
        }
        return $ips;
    }
}
