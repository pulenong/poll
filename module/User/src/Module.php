<?php

declare(strict_types=1);

namespace User;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;
use User\Model\Table\ForgotTable;
use User\Model\Table\RolesTable;
use User\Model\Table\UsersTable;
use User\Plugin\AccountPlugin;
use User\Plugin\AccountPluginFactory;
use User\View\Helper\AccountHelper;

class Module
{
    public function getConfig(): array
    {
        /** @var array $config */
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                ForgotTable::class => function ($container) {
                    $adapter = $container->get(AdapterInterface::class);
                    return new ForgotTable($adapter);
                },
                UsersTable::class => function ($container) {
                    $adapter = $container->get(Adapter::class);
                    return new UsersTable($adapter);
                },
                RolesTable::class => function ($container) {
                    $adapter = $container->get(AdapterInterface::class);
                    return new RolesTable($adapter);
                }
            ]
        ];
    }

    public function getControllerPluginConfig(): array
    {
        return [
            'aliases' => [
                'accountPlugin' => AccountPlugin::class,
            ],
            'factories' => [
                AccountPlugin::class => AccountPluginFactory::class,
            ]
        ];
    }

    public function getViewHelperConfig(): array
    {
        return [
            'aliases' => [
                'accountHelper' => AccountHelper::class,
            ],
            'factories' => [
                AccountHelper::class => AccountPluginFactory::class,
            ]
        ];
    }
}
