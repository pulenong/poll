<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

class UsernameForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('updateUsername');
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => Text::class,
            'name' => 'currentUsername',
            'options' => [
                'label' => 'Current Username',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'minlength' => 2,
                'maxlength' => 25,
                'pattern' => '^[a-zA-Z0-9]{2, 25}$',
                'readonly' => true,
                'title' => 'My account\'s current username',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'newUsername',
            'options' => [
                'label' => 'New Username',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'minlength' => 2,
                'maxlength' => 25,
                'pattern' => '^[a-zA-Z0-9]{2, 25}$',
                'placeholder' => 'Enter Your New Username',
                'title' => 'Username must consist of alphanumeric characters only',
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
                    'timeout' => 3600, // 1hr
                ]
            ]
        ]);

        $this->add([
            'type' => Submit::class,
            'name' => 'changeUsername',
            'attributes' => [
                'value' => 'Save Changes',
                'class' => 'w-100 btn btn-primary btn-lg',
            ]
        ]);
    }
}
