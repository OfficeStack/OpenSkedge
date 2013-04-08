<?php

namespace OpenSkedge\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenSkedge\AppBundle\Entity\Group;

/**
 * Adds the default user roles to the database.
 *
 * @category ORM
 * @package  OpenSkedge\AppBundle\DataFixtures\ORM
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $groupEmployee = new Group();
        $groupEmployee->setName('Employee');
        $groupEmployee->setRole('ROLE_USER');
        $manager->persist($groupEmployee);

        $groupSupervisor = new Group();
        $groupSupervisor->setName('Supervisor');
        $groupSupervisor->setRole('ROLE_ADMIN');
        $manager->persist($groupSupervisor);

        $groupAdmin = new Group();
        $groupAdmin->setName('Admin');
        $groupAdmin->setRole('ROLE_SUPER_ADMIN');
        $manager->persist($groupAdmin);

        $manager->flush();

        $this->addReference('admin-group', $groupAdmin);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
