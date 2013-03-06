<?php

namespace OpenSkedge\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OpenSkedgeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /* Allow CSRF secret token to be set by the environment and override
         * parameters.yml
        */
        if (isset($_SERVER["OPENSKEDGE_SECRET"])) {
            $container->setParameter('secret', $_SERVER["OPENSKEDGE_SECRET"]);
        }

        if (isset($_SERVER["SYMFONY__ADMIN__EMAIL"])) {
            $container->setParameter('admin_email', $_SERVER["SYMFONY__ADMIN__EMAIL"]);
        }

        if (isset($_SERVER["SYMFONY__SENDER__EMAIL"])) {
            $container->setParameter('admin_email', $_SERVER["SYMFONY__SENDER__EMAIL"]);
        }

        // Required for Pagoda Box support
        if (isset($_SERVER["DB1_PORT"])) {
            $container->setParameter('database.port', $_SERVER["DB1_PORT"]);
            $container->setParameter('database.host', $_SERVER["DB1_HOST"]);
            $container->setParameter('database.name', $_SERVER["DB1_NAME"]);
            $container->setParameter('database.user', $_SERVER["DB1_USER"]);
            $container->setParameter('database.password', $_SERVER["DB1_PASS"]);
        }

        if (isset($_SERVER["CACHE1_HOST"])) {
            $container->setParameter('memcache.host', $_SERVER["CACHE1_HOST"]);
            $container->setParameter('memcache.port', $_SERVER["CACHE1_PORT"]);
            if (isset($_SERVER["SYMFONY__MEMCACHE__EXPIRE"])) {
                $container->setParameter('memcache.expire', $_SERVER["SYMFONY__MEMCACHE__EXPIRE"]);
            } else {
                $container->setParameter('memcache.expire', 3600);
            }
        }

        if (is_readable(__DIR__.'/../../../.git/HEAD')) {
            $headref = rtrim(substr(file_get_contents(__DIR__.'/../../../.git/HEAD'), 5));
            if (is_readable(__DIR__.'/../../../.git/'.$headref)) {
                $commit = file_get_contents(__DIR__.'/../../../.git/'.$headref);
                $commit = substr($commit, 0, 7);
            } else {
                $commit = null;
            }
        } else {
            $commit = null;
        }

        $container->setParameter('deploy_commit', (string)$commit);
    }
}
