<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Model\Table\UsersTable;

class ProfileController extends AbstractActionController
{
    /**
     * @param UsersTable $usersTable
     */
    public function __construct(private readonly UsersTable $usersTable)
    {
    }

    /**
     * @return Response|ViewModel
     */
    public function indexAction(): Response|ViewModel
    {
        $username = $this->params()->fromRoute('username');
        if (!$username) {
            return $this->notFoundAction();
        }

        $info = $this->usersTable->findByUsername($username);
        if (!$info) {
            return $this->notFoundAction();
        }

        return new ViewModel(['account' => $info]);
    }
}
