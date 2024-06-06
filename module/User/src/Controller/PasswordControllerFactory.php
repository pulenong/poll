<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Model\Table\ForgotTable;
use User\Model\Table\UsersTable;

class PasswordControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PasswordController
    {
        return new PasswordController(
            $container->get(ForgotTable::class),
            $container->get(UsersTable::class)
        );
    }
}
