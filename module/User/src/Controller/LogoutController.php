<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * @method flashMessenger()
 */
class LogoutController extends AbstractActionController
{
    /**
     * Handles session removal
     *
     * @return Response|ViewModel
     */
    public function indexAction(): Response|ViewModel
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
        }

        $this->flashMessenger()->addInfoMessage('You are signed out');
        return $this->redirect()->toRoute('home');
    }
}
