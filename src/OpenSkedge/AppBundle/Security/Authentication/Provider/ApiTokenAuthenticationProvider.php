<?php

namespace OpenSkedge\AppBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * ApiTokenAuthenticationProvider extends DaoAuthenticationProvider
 * and overrides the checkAuthentication method to authenticate a user
 * with their API tokens instead of passwords.
 *
 * @category AuthenticationProvider
 * @package  OpenSkedge\AppBundle\Security\Authentication\Provider
 * @author   Max Fierke <max@maxfierke.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ApiTokenAuthenticationProvider extends DaoAuthenticationProvider
{
    /**
     * {@inheritdoc}
     */
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        if ("" === ($presentedApiToken = $token->getCredentials())) {
            throw new BadCredentialsException('The presented API key cannot be empty.');
        }

        if (!$user->getApiTokens()->exists(function ($key, $element) use ($presentedApiToken) {
            if ($element->getToken() === $presentedApiToken) {
                return true;
            }
            return false;
        })) {
            throw new BadCredentialsException('The presented API key is invalid.');
        }
    }
}
