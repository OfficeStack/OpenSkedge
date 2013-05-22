<?php

namespace OpenSkedge\AppBundle\DependencyInjection\Security\Factory;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;

use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

/**
 * ApiTokenAuthenticationFactory creates services for HTTP basic authentication using API tokens
 * as passwords.
 *
 * Based on HttpBasicFactory
 *
 * @category AuthenticationProvider
 * @package  OpenSkedge\AppBundle\Security\Authentication\Provider
 * @author   Max Fierke <max@maxfierke.com>, Fabien Potencier <fabien@symfony.com>
 * @license  GNU General Public License, version 3
 * @version  GIT: $Id$
 * @link     https://github.com/maxfierke/OpenSkedge OpenSkedge Github
 */
class ApiTokenAuthenticationFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $provider = 'security.authentication.provider.api_token_auth.'.$id;
        $container
            ->setDefinition($provider, new DefinitionDecorator('security.authentication.provider.api_token_auth'))
            ->replaceArgument(0, new Reference($userProvider))
            ->replaceArgument(2, $id)
        ;

        // entry point
        $entryPointId = $this->createEntryPoint($container, $id, $config, $defaultEntryPoint);

        // listener
        $listenerId = 'security.authentication.listener.basic.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('security.authentication.listener.basic'));
        $listener->replaceArgument(2, $id);
        $listener->replaceArgument(3, new Reference($entryPointId));

        return array($provider, $listenerId, $entryPointId);
    }

    public function getPosition()
    {
        return 'http';
    }

    public function getKey()
    {
        return 'api-token-auth';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('provider')->end()
                ->scalarNode('realm')->defaultValue('OpenSkedge API')->end()
            ->end()
        ;
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        if (null !== $defaultEntryPoint) {
            return $defaultEntryPoint;
        }

        $entryPointId = 'security.authentication.basic_entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('security.authentication.basic_entry_point'))
            ->addArgument($config['realm'])
        ;

        return $entryPointId;
    }
}
