<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class PasswordForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('updatePassword');
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => Password::class,
            'name' => 'currentPassword',
            'options' => [
                'label' => 'Current Password',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'minlength' => 8,
                'maxlength' => 25,
                'pattern' => '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,25}$',
                'placeholder' => 'Enter Your Current Password',
                'title' => 'Provide your account\'s current password',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => Password::class,
            'name' => 'newPassword',
            'options' => [
                'label' => 'New Password',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'minlength' => 8,
                'maxlength' => 25,
                'pattern' => '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,25}$',
                'placeholder' => 'Enter a New Password',
                'title' => 'Password must have at least 8 characters, a lowercase character, an uppercase character and a digit',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => Password::class,
            'name' => 'confirmNewPassword',
            'options' => [
                'label' => 'Confirm New Password',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'minlength' => 8,
                'maxlength' => 25,
                'pattern' => '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,25}$',
                'placeholder' => 'Enter Your New Password Again',
                'title' => 'Password must match that provided above',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => Csrf::class,
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600,
                ],
            ],
        ]);

        $this->add([
            'type' => Submit::class,
            'name' => 'changePassword',
            'attributes' => [
                'value' => 'Save Changes',
                'class' => 'w-100 btn btn-primary btn-lg',
            ]
        ]);
    }
}
