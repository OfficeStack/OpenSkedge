<?php

namespace OpenSkedge\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use OpenSkedge\AppBundle\Entity\Settings;

class LoadSettingsData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $appSettings = new Settings();
        $appSettings->setBrandName('OpenSkedge')
            ->setPruneAfter(12)
            ->setWeekStartDay('sunday')
            ->setWeekStartDayClock('sunday')
            ->setDefaultTimeResolution('1 hour')
            ->setStartHour('06:00:00')
            ->setEndHour('20:00:00');

        $manager->persist($appSettings);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}
