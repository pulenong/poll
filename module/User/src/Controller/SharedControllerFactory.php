<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Model\Table\UsersTable;

class SharedControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): object
    {
        return new $requestedName($container->get(UsersTable::class));
    }
}
