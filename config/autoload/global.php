<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Laminas\Authentication\AuthenticationService;
use Laminas\Db\Adapter;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'abstract_factories' => [
            Adapter\AdapterAbstractServiceFactory::class,
        ],
        'factories' => [
            Adapter\AdapterInterface::class => Adapter\AdapterServiceFactory::class,
            AuthenticationService::class => InvokableFactory::class,
            FlashMessenger::class => InvokableFactory::class
        ],
        'aliases' => [
            Adapter\Adapter::class => Adapter\AdapterInterface::class,
        ],
    ],
    'db' => [
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=pollster;hostname=localhost',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
        ],
    ],
];
