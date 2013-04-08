<?php

namespace OpenSkedge\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use OpenSkedge\AppBundle\Entity\IP;

/**
 * Adds a list of localhost IP addresses to the list of IPs able
 * to utilize time clock functionality in OpenSkedge.
 *
 * @category ORM
 * @package  OpenSkedge\AppBundle\DataFixtures\ORM
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
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
