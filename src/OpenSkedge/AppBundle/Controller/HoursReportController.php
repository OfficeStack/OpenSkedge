<?php
namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use OpenSkedge\AppBundle\Entity\ArchivedClock;
use OpenSkedge\AppBundle\Entity\Clock;
use OpenSkedge\AppBundle\Entity\Schedule;
use OpenSkedge\AppBundle\Entity\User;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

/**
 * HoursReport controller.
 *
 */
class HoursReportController extends Controller
{
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

        $adapter = new ArrayAdapter($archivedClockWeeks);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        return $this->render('OpenSkedgeBundle:HoursReport:index.html.twig', array(
            'entities' => $entities,
            'paginator' => $paginator,
        ));
    }

    public function viewAction($year, $day, $month)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $week = new \DateTime();
        $week->setDate($year, $month, $day);
        $week->setTime(0,0,0);  // Midnight

        $archivedClocks = $em->getRepository('OpenSkedgeBundle:ArchivedClock')->findBy(array('week' => $week));

        $page = $this->container->get('request')->query->get('page', 1);

        $adapter = new ArrayAdapter($archivedClocks);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(15);
        $paginator->setCurrentPage($page);

        $entities = $paginator->getCurrentPageResults();

        return $this->render('OpenSkedgeBundle:HoursReport:view.html.twig', array(
            'week'      => $week,
            'entities'  => $entities,
            'paginator' => $paginator,
        ));
    }

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
        $week->setTime(0,0,0);  // Midnight

        $user = $em->getRepository('OpenSkedgeBundle:User')->find($id);

        if (!$user) {
            $this->createNotFoundException('User entity was not found!');
        }

        $archivedClock = $em->getRepository('OpenSkedgeBundle:ArchivedClock')->findOneBy(array('week' => $week, 'user' => $id));

        if (!$archivedClock) {
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
        foreach($schedulePeriods as $sp) {
            $schedules[] = $em->getRepository('OpenSkedgeBundle:Schedule')->findBy(array('user' => $id, 'schedulePeriod' => $sp->getId()));
        }

        // Create a temporary Schedule entity to store the composite of all the users schedules.
        $scheduled = new Schedule();
        for($i = 0; $i < count($schedulePeriods); $i++) {
            foreach ($schedules[$i] as $s) {
                for($timesect = 0; $timesect < 96; $timesect++) {
                    for($day = 0; $day < 7; $day++) {
                        if($s->getDayOffset($day, $timesect) == '1') {
                                $scheduled->setDayOffset($day, $timesect, '1');
                        }
                    }
                }
            }
        }

        return $this->render('OpenSkedgeBundle:HoursReport:generate.html.twig', array(
            'htime'          => mktime(0,0,0,1,1),
            'user'           => $user,
            'week'           => $week,
            'archivedClock'  => $archivedClock,
            'scheduled'      => $scheduled,
        ));
    }
}
