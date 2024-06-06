<?php

declare(strict_types=1);

namespace User\Form\Register;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

class RegisterForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('newAccount');
        $this->setAttribute('method', 'post');

        // add the username text input field
        $this->add([
            'type' => Text::class,
            'name' => 'username',
            'options' => [
                'label' => 'Username',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'minlength' => 2,
                'maxlength' => 25,
                'pattern' => '^[a-zA-Z0-9]{2,25}$',
                'placeholder' => 'Enter Your Username',
                'title' => 'Username must consist of alphanumeric characters only',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        // add the email address text input field
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
                'title' => 'Provide a valid and working email address',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        // add the birthdate select field
        $this->add([
            'type' => DateSelect::class,
            'name' => 'birthdate',
            'options' => [
                'label' => 'Select Your Date of Birth',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
                'day_attributes' => [
                    'class' => 'form-select mx-2',
                    'id' => 'day',
                ],
                'month_attributes' => [
                    'class' => 'form-select',
                    'id' => 'month',
                ],
                'year_attributes' => [
                    'class' => 'form-select',
                    'id' => 'year',
                ],
                'max_year' => date('Y') - 13,
                'create_empty_option' => true,
                'render_delimiters' => false,
            ],
            'attributes' => [
                'required' => true,
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
                'pattern' => '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,25}$',
                'placeholder' => 'Enter Your Password',
                'title' => 'Password must have atleast 8 characters',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        // add the confirm password input field
        $this->add([
            'type' => Password::class,
            'name' => 'confirmPassword',
            'options' => [
                'label' => 'Confirm Password',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'minlength' => 8,
                'maxlength' => 25,
                'pattern' => '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,25}$',
                'placeholder' => 'Enter Your Password Again',
                'title' => 'Password must match that provided above',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        // add the cross-site-reqest-forgery protection field
        $this->add([
            'type' => Csrf::class,
            'name' => 'signupCsrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600,
                ],
            ],
        ]);

        // add the submit button
        $this->add([
            'type' => Submit::class,
            'name' => 'createAccount',
            'attributes' => [
                'value' => 'Sign up',
                'class' => 'w-100 btn btn-primary btn-lg',
            ]
        ]);
    }
}
