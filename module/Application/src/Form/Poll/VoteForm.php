<?php

declare(strict_types=1);

namespace Application\Form\Poll;

use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class VoteForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('vote');
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => Radio::class,
            'name' => 'optionId',
            'options' => [
                'label_attributes' => [
                    'class' => 'form-label',
                ],
                'disable_inarray_validator' => true,
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-check-input',
            ]
        ]);

        $this->add([
            'type' => Hidden::class,
            'name' => 'userId',
        ]);

        $this->add([
            'type' => Submit::class,
            'name' => 'saveVote',
            'attributes' => [
                'value' => 'Save My Vote',
                'class' => 'btn btn-outline-primary btn-sm'
            ]
        ]);
    }
}
