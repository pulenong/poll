<?php

declare(strict_types=1);

namespace User\Form\Login;

use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('authenticate');
        $this->setAttribute('method', 'post');

        // add the email address input field
        $this->add([
            'type' => Email::class,
            'name' => 'email',
            'options' => [
                'label' => 'Email Address',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 128,
                'pattern' => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                'placeholder' => 'Enter Your Email Address',
                'title' => 'Provide your account\'s email address',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        // add the password input field
        $this->add([
            'type' => Password::class,
            'name' => 'password',
            'options' => [
                'label' => 'Password',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'minlength' => 8,
                'maxlength' => 25,
                'pattern' => '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,25}',
                'placeholder' => 'Enter Your Password',
                'title' => 'Provide your account\'s password',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => Checkbox::class,
            'name' => 'recall',
            'options' => [
                'label' => 'Remember me?',
                'label_attributes' => [
                    'class' => 'form-check-label',
                ],
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
            ],
            'attributes' => [
                'value' => '0',
                'class' => 'form-check-input',
            ]
        ]);

        $this->add([
            'type' => Hidden::class,
            'name' => 'returnUrl',
        ]);

        $this->add([
            'type' => Csrf::class,
            'name' => 'loginCsrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600,
                ],
            ],
        ]);

        $this->add([
            'type' => Submit::class,
            'name' => 'accountLogin',
            'attributes' => [
                'value' => 'Sign in',
                'class' => 'w-100 btn btn-primary btn-lg'
            ]
        ]);
    }
}
