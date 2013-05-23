<?php

namespace OpenSkedge\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

use OpenSkedge\AppBundle\Entity\User;

/**
 * Controller for manipulating time clock entities
 *
 * @category Controller
 * @package  OpenSkedge\AppBundle\Controller
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ClockController extends Controller
{
    /**
     * Mark the user as clocked in & update the clock in timestamp.
     * If it's a new week from their last clock in, backup their time clock
     * to an ArchivedClock entity.
     *
     * @param Request $request The user's request object
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function clockInAction(Request $request)
    {
        // Ensure the accessing user is authenticated and authorized ROLE_USER
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        // Grab a few services.
        $appSettingsService = $this->get('app_settings');

        /* If running on Pagoda Box, get the user's IP directly from HTTP_X_FORWARDED_FOR,
         * otherwise, go to Request::getClientIp()
         */
        $clientIp = (isset($_ENV['PAGODABOX']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $request->getClientIp());
        if (!in_array($clientIp, $appSettingsService->getAllowedClockIps())) {
            throw new AccessDeniedException();
        }

        $this->get('clock_utils')->clockIn($this->getUser());

        if ($request->getContentType() === 'json' or $request->getContentType() === 'xml') {
            return new Response(
                '',
                200,
                array('content-type' => 'application/'.$request->getContentType())
            );
        } else {
            return $this->redirect($this->generateUrl('dashboard'));
        }
    }

    /**
     * Mark the user as clocked out & update the relevant time records.
     *
     * @param Request $request The user's request object
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function clockOutAction(Request $request)
    {
        // Ensure the accessing user is authenticated and authorized ROLE_USER
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        /* If running on Pagoda Box, get the user's IP directly from HTTP_X_FORWARDED_FOR,
         * otherwise, go to Request::getClientIp()
         */
        $clientIp = (isset($_ENV['PAGODABOX']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $request->getClientIp());
        if (!in_array($clientIp, $this->get('app_settings')->getAllowedClockIps())) {
            throw new AccessDeniedException();
        }

        $this->get('clock_utils')->clockOut($this->getUser());

        if ($request->getContentType() === 'json' or $request->getContentType() === 'xml') {
            return new Response(
                '',
                200,
                array('content-type' => 'application/'.$request->getContentType())
            );
        } else {
            return $this->redirect($this->generateUrl('dashboard'));
        }
    }
}
