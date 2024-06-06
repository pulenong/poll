<?php

declare(strict_types=1);

namespace User\Form\Password;

use Laminas\Captcha\ReCaptcha;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class ForgotForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('lostPassword');
        $this->setAttribute('method', 'post');

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

        $this->add([
            'type' => Captcha::class,
            'name' => 'turing',
            'options' => [
                'label' => 'Human Verification?',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
                'captcha' => new ReCaptcha([
                    // to get values from Google visit www.google.com/recaptcha/admin/create
                    'secret_key' => '6LdY__gnAAAAAKTz-T8q46tbgnuSSSctsGcdEInz',
                    'site_key' => '6LdY__gnAAAAAGSDKFx_go2RFN8BzJvyBQvRDl30'
                ])
            ]
        ]);

        $this->add([
            'type' => Submit::class,
            'name' => 'forgotPassword',
            'attributes' => [
                'value' => 'Send Message',
                'class' => 'w-100 btn btn-primary btn-lg',
            ]
        ]);
    }
}
