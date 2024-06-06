<?php

declare(strict_types=1);

namespace User\Plugin;

use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use User\Model\Entity\UserEntity;
use User\Model\Table\UsersTable;

class AccountPlugin extends AbstractPlugin
{
    /**
     * @var UsersTable|null
     */
    protected ?UsersTable $usersTable;

    /**
     * @var AuthenticationServiceInterface|null
     */
    protected ?AuthenticationServiceInterface $authenticationService;

    public function getUsersTable(): ?UsersTable
    {
        return $this->usersTable;
    }

    public function setUsersTable(?UsersTable $usersTable): AccountPlugin
    {
        $this->usersTable = $usersTable;
        return $this;
    }

    public function getAuthenticationService(): ?AuthenticationServiceInterface
    {
        return $this->authenticationService;
    }

    public function setAuthenticationService(AuthenticationServiceInterface $authenticationService): AccountPlugin
    {
        $this->authenticationService = $authenticationService;
        return $this;
    }

    public function __invoke(): ?UserEntity
    {
        if (!$this->authenticationService->hasIdentity()) {
            return null;
        }

        return $this->getUsersTable()->findById((int) $this->authenticationService->getIdentity()->user_id);
    }
}
