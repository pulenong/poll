<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Form\Register\RegisterForm;
use User\Model\Table\UsersTable;

class RegisterController extends AbstractActionController
{
    /**
     * @param UsersTable $usersTable
     */
    public function __construct(private readonly UsersTable $usersTable)
    {
    }

    /**
     * Handles account creation
     *
     * @return Response|ViewModel
     */
    public function indexAction(): Response|ViewModel
    {
        // Do not allow logged in users to access this page
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home'); // @todo redirect to profile route
        }

        $registerForm = new RegisterForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $registerForm->setInputFilter($this->usersTable->getRegisterFormSanitizer());
            $registerForm->setData($request->getPost()->toArray());

            if ($registerForm->isValid()) {
                $data = $registerForm->getData();

                try {
                    $this->usersTable->insertAccount($data);
                    $this->flashMessenger()->addSuccessMessage('Account successfully created. You can now login.');

                    return $this->redirect()->toRoute('login');
                } catch (RuntimeException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $registerForm]);
    }
}
