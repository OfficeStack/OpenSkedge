<?php

namespace OpenSkedge\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use OpenSkedge\AppBundle\Entity\IP;


class LoadIPData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $localhost_ipv4 = new IP();
        $localhost_ipv4->setIP('127.0.0.1')
            ->setName('Server')
            ->setClockEnabled(true);
        $manager->persist($localhost_ipv4);

        $localhost_ipv6_ll1 = new IP();
        $localhost_ipv6_ll1->setIP('::1')
            ->setName('Server')
            ->setClockEnabled(true);
        $manager->persist($localhost_ipv6_ll1);

        $localhost_ipv6_ll2 = new IP();
        $localhost_ipv6_ll2->setIP('fe80::1')
            ->setName('Server')
            ->setClockEnabled(true);
        $manager->persist($localhost_ipv6_ll2);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5; // the order in which fixtures will be loaded
    }
}
