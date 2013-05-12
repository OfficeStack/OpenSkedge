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
        if (getenv("OPENSKEDGE_SECRET")) {
            $container->setParameter('secret', getenv("OPENSKEDGE_SECRET"));
        }

        if (getenv("SYMFONY__SENDER__EMAIL")) {
            $container->setParameter('sender_email', getenv("SYMFONY__SENDER__EMAIL"));
        }

        if (isset($_ENV["PAGODABOX"])) {
            if (isset($_SERVER["DB1_PORT"])) { // Pagoda Box MySQL database detection
                $container->setParameter('database.port', $_SERVER["DB1_PORT"]);
                $container->setParameter('database.host', $_SERVER["DB1_HOST"]);
                $container->setParameter('database.name', $_SERVER["DB1_NAME"]);
                $container->setParameter('database.user', $_SERVER["DB1_USER"]);
                $container->setParameter('database.password', $_SERVER["DB1_PASS"]);
            }

            if (isset($_SERVER["CACHE1_HOST"])) { // Pagoda Box memcache detection
                $container->setParameter('memcache.host', $_SERVER["CACHE1_HOST"]);
                $container->setParameter('memcache.port', $_SERVER["CACHE1_PORT"]);
                if (getenv("SYMFONY__MEMCACHE__EXPIRE")) {
                    $container->setParameter('memcache.expire', getenv("SYMFONY__MEMCACHE__EXPIRE"));
                } else {
                    $container->setParameter('memcache.expire', 3600);
                }
            }
        }

        if (extension_loaded('apc') && ini_get('apc.enabled')) {
            $container->setParameter('doctrine.orm.metadata_cache_driver', 'apc');
            $container->setParameter('doctrine.orm.result_cache_driver', 'apc');
            $container->setParameter('doctrine.orm.query_cache_driver', 'apc');
        }


        // Get the commit id from HEAD if we're deployed as a git repo.
        if (is_readable(__DIR__.'/../../../.git/HEAD')) {
            $headref = rtrim(substr(file_get_contents(__DIR__.'/../../../.git/HEAD'), 5));
            if (is_readable(__DIR__.'/../../../.git/'.$headref)) {
                $commit = file_get_contents(__DIR__.'/../../../.git/'.$headref);
                $commit = substr($commit, 0, 7);
            } else {
                $commit = null;
            }
        } else {
            // Not deployed as a git repo, so we have no commit id.
            $commit = null;
        }

        $container->setParameter('deploy_commit', (string)$commit);
    }
}
