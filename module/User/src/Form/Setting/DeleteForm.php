<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class DeleteForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('deactivate');
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => Submit::class,
            'name' => 'deleteAccount',
            'attributes' => [
                'value' => 'Yes',
                'class' => 'w-100 btn btn-outline-danger btn-lg',
            ]
        ]);
    }
}
