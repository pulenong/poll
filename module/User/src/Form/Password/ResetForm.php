<?php

declare(strict_types=1);

namespace User\Form\Password;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class ResetForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('alterPassword');
        $this->setAttribute('method', 'post');

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
                'placeholder' => 'Enter Your New Password',
                'title' => 'Password must have at least 8 characters',
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
                'title' => 'Password must match with that provided above',
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
                    'timeout' => 600, // 10 minutes
                ]
            ]
        ]);

        $this->add([
            'type' => Submit::class,
            'name' => 'resetPassword',
            'attributes' => [
                'value' => 'Reset Password',
                'class' => 'w-100 btn btn-primary btn-lg',
            ]
        ]);
    }
}
