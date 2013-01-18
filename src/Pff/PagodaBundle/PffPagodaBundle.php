<?php
namespace Pff\PagodaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PffPagodaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // detect auto config
        if (isset($_SERVER["DB1_PORT"]))
        {
            $container->setParameter('database.port', $_SERVER["DB1_PORT"]);
            $container->setParameter('database.host', $_SERVER["DB1_HOST"]);
            $container->setParameter('database.name', $_SERVER["DB1_NAME"]);
            $container->setParameter('database.user', $_SERVER["DB1_USER"]);
            $container->setParameter('database.password', $_SERVER["DB1_PASS"]);
        }
    }
}
