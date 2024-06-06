<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class EmailForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('editEmail');
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => Email::class,
            'name' => 'currentEmail',
            'options' => [
                'label' => 'Current Email Address',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 128,
                'pattern' => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                'readonly' => true,
                'title' => 'My account\'s current email address',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Email::class,
            'name' => 'newEmail',
            'options' => [
                'label' => 'New Email Address',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 128,
                'pattern' => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                'placeholder' => 'Enter Your New Email Address',
                'title' => 'Provide a valid and working email address',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ],
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
            'name' => 'changeEmail',
            'attributes' => [
                'value' => 'Save Changes',
                'class' => 'w-100 btn btn-primary btn-lg'
            ]
        ]);
    }
}
