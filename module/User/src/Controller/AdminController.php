<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Model\Table\UsersTable;
use User\Service\UrlService;

class AdminController extends AbstractActionController
{
    public function __construct(private readonly UsersTable $usersTable)
    {
    }

    public function indexAction(): Response|ViewModel
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login', ['returnUrl' => UrlService::encode($this->getRequest()->getRequestUri())]);
        }

        $paginator = $this->usersTable->findAll(true);
        $page = (int) $this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(5);

        return new ViewModel(['paginator' => $paginator]);
    }
}
