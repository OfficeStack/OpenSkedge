<?php

namespace OpenSkedge\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenSkedge\AppBundle\Entity\Clock;

/**
 * Adds a Clock entity for the admin User entity.
 *
 * @category ORM
 * @package  OpenSkedge\AppBundle\DataFixtures\ORM
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class LoadClockData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $adminClock = new Clock();
        $adminClock->setUser($this->getReference('admin-user'));

        $manager->persist($adminClock);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
