<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Db\Adapter\Adapter;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Model\Table\UsersTable;

class LoginControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoginController
    {
        return new LoginController(
            $container->get(Adapter::class),
            $container->get(UsersTable::class)
        );
    }
}
