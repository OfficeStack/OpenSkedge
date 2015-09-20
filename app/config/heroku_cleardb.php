<?php
    if ($db_url = getenv('CLEARDB_DATABASE_URL')) {
        $db = parse_url($db_url);
        $container->setParameter('database_driver', 'pdo_mysql');
        $container->setParameter('database_host', $db['host']);
        $container->setParameter('database_port', $db['port']);
        $container->setParameter('database_name', substr($db["path"], 1));
        $container->setParameter('database_user', $db['user']);
        $container->setParameter('database_password', $db['pass']);
    }
