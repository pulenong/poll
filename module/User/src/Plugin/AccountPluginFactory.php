<?php

declare(strict_types=1);

namespace User\Plugin;

use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Model\Table\UsersTable;

class AccountPluginFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AccountPlugin
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AccountPlugin
    {
        $accountPlugin = new AccountPlugin();

        if ($container->has(AuthenticationService::class)) {
            $accountPlugin->setAuthenticationService($container->get(AuthenticationService::class));
        } elseif ($container->has(AuthenticationServiceInterface::class)) {
            $accountPlugin->setAuthenticationService($container->get(AuthenticationServiceInterface::class));
        }

        if ($container->has(UsersTable::class)) {
            $accountPlugin->setUsersTable($container->get(UsersTable::class));
        }

        return $accountPlugin;
    }
}
