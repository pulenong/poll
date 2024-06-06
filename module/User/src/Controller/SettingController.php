<?php

declare(strict_types=1);

namespace User\Controller;

use Exception;
use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\BcryptSha;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Form\Setting\AvatarForm;
use User\Form\Setting\DeleteForm;
use User\Form\Setting\EmailForm;
use User\Form\Setting\PasswordForm;
use User\Form\Setting\UsernameForm;
use User\Model\Table\UsersTable;
use User\Service\PictureService;
use User\Service\UrlService;

/**
 * @method accountPlugin()
 * @method flashMessenger()
 */
class SettingController extends AbstractActionController
{
    /**
     * @param UsersTable $usersTable
     */
    public function __construct(private readonly UsersTable $usersTable)
    {
    }

    /**
     * Handles updating user's profile picture
     *
     * @return Response|ViewModel
     * @throws Exception
     */
    public function avatarAction(): Response|ViewModel
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login', ['returnUrl' => UrlService::encode($this->getRequest()->getRequestUri())]);
        }

        $avatarForm = new AvatarForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $avatarForm->setInputFilter($this->usersTable->getAvatarFormSanitizer());

            // Note that if you were submitting a form that has both text and images
            // you would handle such a scenario this way:
            /*$avatarForm->setData(
                array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                )
            );*/

            $avatarForm->setData($request->getFiles()->toArray());

            if ($avatarForm->isValid()) {
                $data = $avatarForm->getData();

                if (file_exists($data['picture']['tmp_name'])) {

                    $picture = basename($data['picture']['tmp_name']);

                    $thumbnail = new PictureService($data['picture']['tmp_name']);
                    $thumbnail->resizeToHeight(200);
                    $thumbnail->save('public'.DIRECTORY_SEPARATOR.'profile'.DIRECTORY_SEPARATOR.$picture);

                    $this->usersTable->updatePicture($picture, (int) $this->accountPlugin()->getUserId());
                    unlink('public/temporary/'.$picture);

                    $this->flashMessenger()->addSuccessMessage('Profile picture uploaded successfully');
                    return $this->redirect()->toRoute('profile', ['username' => mb_strtolower($this->accountPlugin()->getUsername())]);
                } else {
                    $this->flashMessenger()->addErrorMessage('No image to process! Try re-uploading...');
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $avatarForm]);
    }

    /**
     * Handles deleting user's account
     *
     * @return Response|ViewModel
     */
    public function deleteAction(): Response|ViewModel
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login', ['returnUrl' => UrlService::encode($this->getRequest()->getRequestUri())]);
        }

        $deleteForm = new DeleteForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $deleteForm->setData($request->getPost()->toArray());

            if ($deleteForm->isValid()) {
                if ($request->getPost()->get('deleteAccount') == 'Yes') {
                    $this->usersTable->deleteAccount((int) $this->accountPlugin()->getUserId());
                    // clear all sessions for this user as well
                    return $this->redirect()->toRoute('logout');
                }
            }
        }

        return new ViewModel(['form' => $deleteForm]);
    }

    /**
     * Handles changing user's email address
     *
     * @return Response|ViewModel
     */
    public function emailAction(): Response|ViewModel
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login', ['returnUrl' => UrlService::encode($this->getRequest()->getRequestUri())]);
        }

        $emailForm = new EmailForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $emailForm->setInputFilter($this->usersTable->getEmailFormSanitizer());
            $emailForm->setData($request->getPost()->toArray());

            if ($emailForm->isValid()) {
                $data = $emailForm->getData();

                try {
                    $this->usersTable->updateEmail($data['newEmail'], (int) $this->accountPlugin()->getUserId());
                    $this->flashMessenger()->addSuccessMessage('Email address successfully updated.');

                    return $this->redirect()->toRoute('settings', ['action' => 'email']); // might have to change to index later on.
                } catch (RuntimeException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $emailForm]);
    }

    public function indexAction(): Response|ViewModel
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login', ['returnUrl' => UrlService::encode($this->getRequest()->getRequestUri())]);
        }

        return new ViewModel();
    }

    /**
     * Handles updating user's password
     *
     * @return Response|ViewModel
     */
    public function passwordAction(): Response|ViewModel
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login', ['returnUrl' => UrlService::encode($this->getRequest()->getRequestUri())]);
        }

        $passwordForm = new PasswordForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $passwordForm->setInputFilter($this->usersTable->getPasswordFormSanitizer());
            $passwordForm->setData($request->getPost()->toArray());

            if ($passwordForm->isValid()) {
                $data = $passwordForm->getData();

                try {
                    if ((new BcryptSha())->verify($data['currentPassword'], $this->accountPlugin()->getPassword())) {
                        $this->usersTable->updatePassword($data['newPassword'], (int) $this->accountPlugin()->getUserId());
                    }

                    $this->flashMessenger()->addSuccessMessage('Password successfully changed. You can now login with your new password');
                    return $this->redirect()->toRoute('logout');
                } catch (RuntimeException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $passwordForm]);
    }

    /**
     * Handles updating user's username
     *
     * @return Response|ViewModel
     */
    public function usernameAction(): Response|ViewModel
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login', ['returnUrl' => UrlService::encode($this->getRequest()->getRequestUri())]);
        }

        $usernameForm = new UsernameForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $usernameForm->setInputFilter($this->usersTable->getUsernameFormSanitizer());
            $usernameForm->setData($request->getPost()->toArray());

            if ($usernameForm->isValid()) {
                $data = $usernameForm->getData();

                try {
                    $this->usersTable->updateUsername($data['newUsername'], (int) $this->accountPlugin()->getUserId());
                    $this->flashMessenger()->addSuccessMessage('Username successfully changed.');

                    return $this->redirect()->toRoute('settings', ['action' => 'username']);

                } catch (RuntimeException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $usernameForm]);
    }
}
