<?php

namespace OpenSkedge\AppBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;

class AppSettingsService
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function getAppSettings()
    {
        // Grab the settings row from os_settings
        $appSettings = $this->em->getRepository('OpenSkedgeBundle:Settings')->find(1);
        if (!$appSettings instanceof \OpenSkedge\AppBundle\Entity\Settings) {
            throw new \Exception('OpenSkedge settings could not be found');
        }
        return $appSettings;
    }

    public function getAllowedClockIps()
    {
        $ipObjs = $this->em->getRepository('OpenSkedgeBundle:IP')->findBy(array('clockEnabled'=> true));
        $ips = array();
        foreach ($ipObjs as $ipObj) {
            $ips[] = $ipObj->getIp();
        }
        return $ips;
    }
}
