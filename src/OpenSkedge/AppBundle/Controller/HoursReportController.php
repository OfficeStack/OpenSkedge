<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use OpenSkedge\AppBundle\Entity\ArchivedClock;
use OpenSkedge\AppBundle\Entity\Clock;
use OpenSkedge\AppBundle\Entity\Schedule;
use OpenSkedge\AppBundle\Entity\User;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidMaxPerPageException;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * Controller for generating time clock reports
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class HoursReportController extends Controller
{
    /**
     * Lists all weeks with ArchivedClock entities
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $archivedClockWeeks = $em->createQueryBuilder()
            ->select('DISTINCT ac.week')
            ->from('OpenSkedgeBundle:ArchivedClock', 'ac')
            ->orderBy('ac.week', 'DESC')
            ->getQuery()
            ->getResult();

        $page = $this->container->get('request')->query->get('page', 1);
        $limit = $this->container->get('request')->query->get('limit', 15);

        $adapter = new ArrayAdapter($archivedClockWeeks);
        $paginator = new Pagerfanta($adapter);

        try {
            $paginator->setMaxPerPage($limit);
            $paginator->setCurrentPage($page);

            $entities = $paginator->getCurrentPageResults();
        } catch (NotValidMaxPerPageException $e) {
            throw new HttpException(400, 'Not a valid limit', $e, array(), $e->getCode());
        } catch (NotValidCurrentPageException $e) {
            throw $this->createNotFoundException('Page does not exist.');
        }

        return $this->render('OpenSkedgeBundle:HoursReport:index.html.twig', array(
            'entities' => $entities,
            'paginator' => $paginator,
        ));
    }

    /**
     * Lists all ArchivedClock entities within the requested week
     *
     * @param integer $year  Year in YYYY format
     * @param integer $month Month in MM format
     * @param integer $day   Day in DD format
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($year, $month, $day)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $week = new \DateTime();
        $week->setDate($year, $month, $day);
        $week->setTime(0, 0, 0);  // Midnight

        $week = $this->container->get('dt_utils')->getFirstDayOfWeek($week);

        $archivedClocksQB = $em->createQueryBuilder()
                             ->select('ac')
                             ->from('OpenSkedgeBundle:ArchivedClock', 'ac')
                             ->where('ac.week = :week')
                             ->setParameter('week', $week);

        $page = $this->container->get('request')->query->get('page', 1);
        $limit = $this->container->get('request')->query->get('limit', 15);

        $adapter = new DoctrineORMAdapter($archivedClocksQB);
        $paginator = new Pagerfanta($adapter);

        try {
            $paginator->setMaxPerPage($limit);
            $paginator->setCurrentPage($page);

            $entities = $paginator->getCurrentPageResults();
        } catch (NotValidMaxPerPageException $e) {
            throw new HttpException(400, 'Not a valid limit', $e, array(), $e->getCode());
        } catch (NotValidCurrentPageException $e) {
            throw $this->createNotFoundException('Page does not exist.');
        }

        return $this->render('OpenSkedgeBundle:HoursReport:view.html.twig', array(
            'week'      => $week,
            'entities'  => $entities,
            'paginator' => $paginator,
        ));
    }

    /**
     * Generates a time clock report for the specified user
     *
     * @param Request $request The user's request object
     * @param integer $id      ID of user
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function generateAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $year = $request->request->get('year');
        $day = $request->request->get('day');
        $month = $request->request->get('month');

        $week = new \DateTime();
        $week->setDate($year, $month, $day);
        $week->setTime(0, 0, 0);  // Midnight

        $dtUtils = $this->container->get('dt_utils');
        $week = $dtUtils->getFirstDayOfWeek($week);
        $nextWeek = clone $week;
        $nextWeek->modify("+7 days");

        $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

        if (!$user instanceof User) {
            $this->createNotFoundException('User entity was not found!');
        }

        $archivedClock = $em->getRepository('OpenSkedgeBundle:ArchivedClock')->findOneBy(array('week' => $week, 'user' => $id));

        if (!$archivedClock instanceof ArchivedClock) {
            $this->createNotFoundException('User clock data not found for specified week!');
        }

        $schedulePeriods = $em->createQueryBuilder()
            ->select('sp')
            ->from('OpenSkedgeBundle:SchedulePeriod', 'sp')
            ->where('sp.startTime <= :week')
            ->andWhere('sp.endTime >= :week')
            ->setParameter('week', $week)
            ->getQuery()
            ->getResult();

        $schedules = array();
        foreach ($schedulePeriods as $sp) {
            $schedules[] = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array('user' => $id, 'schedulePeriod' => $sp->getId()));
        }

        // Create a temporary Schedule entity to store the composite of all the users schedules.
        $scheduled = new Schedule();
        for ($i = 0; $i < count($schedulePeriods); $i++) {
            foreach ($schedules[$i] as $s) {
                for ($timesect = 0; $timesect < 96; $timesect++) {
                    for ($day = 0; $day < 7; $day++) {
                        if ($s->getDayOffset($day, $timesect) == '1') {
                                $scheduled->setDayOffset($day, $timesect, '1');
                        }
                    }
                }
            }
        }

        $shifts = $em->createQuery("SELECT sh FROM OpenSkedgeBundle:Shift sh
            WHERE (sh.pickedUpBy = :user AND sh.startTime >= :week AND sh.endTime < :nextWeek AND sh.status != 'unapproved')")
            ->setParameter('user', $user)
            ->setParameter('week', $week)
            ->setParameter('nextWeek', $nextWeek)
            ->getResult();

        foreach ($shifts as $shift) {
            $startIndex = $dtUtils->getIndexFromTime($shift->getStartTime());
            $endIndex = $dtUtils->getIndexFromTime($shift->getEndTime());
            // Update the schedule composite to mark shifts they had picked up.
            if ($shift->getStartTime()->format('w') === $shift->getEndTime()->format('w')) {
                $day = $shift->getStartTime()->format('w');
                for ($i = $startIndex; $i < $endIndex; $i++) {
                    $scheduled->setDayOffset($day, $i, '1');
                }
            } else if ($shift->getStartTime()->format('w') === $shift->getEndTime()->format('w')-1) {
                $day = $shift->getStartTime()->format('w');
                for ($i = $startIndex; $i < 96; $i++) {
                    $scheduled->setDayOffset($day, $i, '1');
                }
                for ($i = 0; $i < $endIndex; $i++) {
                    $scheduled->setDayOffset($day+1, $i, '1');
                }
            }
        }

        return $this->render('OpenSkedgeBundle:HoursReport:generate.html.twig', array(
            'htime'          => new \DateTime("midnight today", new \DateTimeZone("UTC")),
            'user'           => $user,
            'week'           => $week,
            'archivedClock'  => $archivedClock,
            'scheduled'      => $scheduled,
        ));
    }
}
