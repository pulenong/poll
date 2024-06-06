<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Form\Password\ForgotForm;
use User\Form\Password\ResetForm;
use User\Model\Table\ForgotTable;
use User\Model\Table\UsersTable;

/**
 * @method flashMessenger()
 */
class PasswordController extends AbstractActionController
{
    /**
     * @param ForgotTable $forgotTable
     * @param UsersTable $usersTable
     */
    public function __construct(private readonly ForgotTable $forgotTable, private readonly UsersTable $usersTable)
    { 
    }

    /**
     * Sends user a message with password reset link
     *
     * @return Response|ViewModel
     */
    public function forgotAction(): Response|ViewModel
    {
        // prevent logged-in users from accessing this method
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $forgotForm = new ForgotForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $forgotForm->setInputFilter($this->usersTable->getForgotFormSanitizer());
            $forgotForm->setData($request->getPost()->toArray());

            if ($forgotForm->isValid()) {
                $data = $forgotForm->getData();

                try {
                    $user = $this->usersTable->findByEmail($data['email']);
                    // delete any token that may belong to this user from the forgot table
                    $this->forgotTable->deleteToken((int)$user->getUserId());
                    // generate a new token for this user
                    $token = $this->forgotTable->generateToken(19);
                    // save the newly generated token
                    $this->forgotTable->insertToken($token, (int)$user->getUserId());

                    // fetch template message from the forgot.tpl file
                    $file = dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'forgot.tpl';
                    $file = file_get_contents($file);
                    $body = str_replace('#USERNAME#', $user->getUsername(), $file);
                    $link = $_SERVER['HTTP_HOST'] . '/reset/' . $user->getUserId() . '/' . $token;
                    $body = str_replace('#LINK#', $link, $body);

                    // prepare to send message to user
                    $message = new Message();
                    $message->setFrom('useYourRealEmail@outlook.com')
                        ->addTo($user->getEmail())
                        ->setSubject('I Forgot My Password')
                        ->setBody($body);
                    
                    // send message if all valid
                    if ($message->isValid()) {
                        $transport = new SmtpTransport();
                        $options = new SmtpOptions([
                            'host' => 'smtp.outlook.com', // you can use gmail, or yahoo, etc. JUst replace with the right host details
                            'port' => 587,
                            'connection_class' => 'login',
                            'connection_config' => [
                                'username' => 'useYourRealEmail@outlook.com', // enter your email address
                                'password' => 'enterYourSecureEmailPassword', // enter your email password
                                'ssl' => 'tls',
                            ]
                        ]);

                        $transport->setOptions($options);
                        $transport->send($message);                        
                    }

                    $this->flashMessenger()->addInfoMessage(
                        'Check your email inbox or junk folder for more details'
                    );
                    return $this->redirect()->toRoute('home');

                } catch (RuntimeException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $forgotForm]);
    }

    /**
     * Handles resetting password
     *
     * @return Response|ViewModel
     */
    public function resetAction(): Response|ViewModel
    {
        // do not allow logged-in users
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        // fetch details from the route
        $userId = $this->params()->fromRoute('id');
        $token = $this->params()->fromRoute('token');

        // make sure the fetched details are not empty
        if (empty($userId) || empty($token)) {
            return $this->notFoundAction();
        }

        // verify the fetched userId data
        $user = $this->usersTable->findById((int) $userId);
        if (!$user) {
            return $this->notFoundAction();
        }

        // next let us remove old token that may be lingering in the forgot table
        $this->forgotTable->deleteOldTokens();

        // check and verify token data
        $verify = $this->forgotTable->findToken($token, (int) $user->getUserId()); // could have just used $userId
        if (!$verify) {
            $this->flashMessenger()->addWarningMessage('Token data is not available or is invalid! Request a new token.');
            return $this->redirect()->toRoute('forgot');
        }

        $resetForm = new ResetForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $resetForm->setInputFilter($this->usersTable->getResetFormSanitizer());
            $resetForm->setData($request->getPost()->toArray());

            if ($resetForm->isValid()) {
                $data = $resetForm->getData();

                try {
                    if ($this->usersTable->updatePassword($data['newPassword'], (int) $user->getUserId())) {
                        $this->forgotTable->deleteToken((int) $user->getUserId());
                    }

                    $this->flashMessenger()->addSuccessMessage('Password successfully reset. You can now login.');
                    return $this->redirect()->toRoute('login');
                } catch (RuntimeException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $resetForm]);
    }
}
