<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Exception\ExceptionInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\BcryptSha;
use Laminas\Db\Adapter\Adapter;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\SessionManager;
use Laminas\View\Model\ViewModel;
use User\Form\Login\LoginForm;
use User\Model\Table\UsersTable;
use User\Service\UrlService;

/**
 * @method flashMessenger()
 */
class LoginController extends AbstractActionController
{
    /**
     * @param Adapter $adapter
     * @param UsersTable $usersTable
     */
    public function __construct(private readonly Adapter $adapter, private readonly UsersTable $usersTable)
    {
    }

    /**
     * Handles authentication
     *
     * @return Response|ViewModel
     * @throws ExceptionInterface
     */
    public function indexAction(): Response|ViewModel
    {
        // do not allow any logged-in users to access this method
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home'); // @todo redirect to profile or member when created
        }

        $loginForm = new LoginForm();
        $loginForm->get('returnUrl')->setValue(
            $this->getEvent()->getRouteMatch()->getParam('returnUrl')
        );

        $request = $this->getRequest();
        if ($request->isPost()) {
            $loginForm->setInputFilter($this->usersTable->getLoginFormSanitizer());
            $loginForm->setData($request->getPost()->toArray());

            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                $password = $data['password'];
                $hash = (new BcryptSha())->create($password);

                $validationCallback = function ($hash, $password) {
                    return (new BcryptSha())->verify($password, $hash);
                };

                $authAdapter = new CallbackCheckAdapter($this->adapter);
                $authAdapter->setTableName($this->usersTable->getTable())
                    ->setIdentityColumn('email')
                    ->setCredentialColumn('password')
                    ->setCredentialValidationCallback($validationCallback);

                // insert values from the login form into the methods
                $authAdapter->setIdentity($data['email'])
                    ->setCredential($data['password']);
                
                $returnUrl = $this->params()->fromPost('returnUrl');

                $result = $auth->authenticate($authAdapter);
                switch ($result->getCode()) {
                    case Result::FAILURE_IDENTITY_NOT_FOUND:
                         # non-existent identity
                        $this->flashMessenger()->addErrorMessage('Unrecognized email address!');
                        return $this->redirect()->refresh();
                        break;
                    
                    case Result::FAILURE_CREDENTIAL_INVALID:
                        # invalid credential
                        $this->flashMessenger()->addErrorMessage('Incorrect password!');
                        return $this->redirect()->refresh();
                        break;

                    case Result::SUCCESS:
                        # successful authentication
                        if ($data['recall'] == '1') {
                            $ssm = new SessionManager();
                            $ttl = 1814400; // time to live - 21 days in seconds
                            $ssm->rememberMe($ttl);
                        }

                        $storage = $auth->getStorage();
                        $storage->write($authAdapter->getResultRowObject(null, null));

                        if (!empty($returnUrl)) {
                            return $this->redirect()->toUrl(UrlService::decode($returnUrl));
                        }

                        // to highlight successful login let us add a flash message for now
                        $this->flashMessenger()->addSuccessMessage('You have successfully logged in');
                        return $this->redirect()->toRoute('home'); // @todo add the profile route when it has been created
                        break;

                    default:
                        # other failure
                        $this->flashMessenger()->addErrorMessage('Authentication failed!');
                        return $this->redirect()->refresh();
                        break;
                }
            }

        }

        return new ViewModel(['form' => $loginForm]);
    }
}
