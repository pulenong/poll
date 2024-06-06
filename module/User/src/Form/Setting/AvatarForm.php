<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element\File;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class AvatarForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('uploader');
        $this->setAttributes([
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ]);

        $this->add([
            'type' => File::class,
            'name' => 'picture',
            'options' => [
                'label' => 'Upload Profile Picture',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'multiple' => false,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => Submit::class,
            'name' => 'uploadPicture',
            'attributes' => [
                'value' => 'Upload Picture',
                'class' => 'w-100 btn btn-primary btn-lg',
            ]
        ]);
    }
}
