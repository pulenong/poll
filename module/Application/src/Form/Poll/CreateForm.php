<?php

declare(strict_types=1);

namespace Application\Form\Poll;

use Application\Model\Table\CategoriesTable;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Form;

class CreateForm extends Form
{
    public function __construct(protected CategoriesTable $categoriesTable)
    {
        parent::__construct('newPoll');
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => Text::class,
            'name' => 'title',
            'options' => [
                'label' => 'Poll Title',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 100,
                'placeholder' => 'Enter a title',
                'title' => 'Provide a poll title',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => Select::class,
            'name' => 'timeout',
            'options' => [
                'label' => 'Poll Ends In?',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
                'empty_option' => 'Select...',
                'value_options' => [
                    '1 day' => '1 day',
                    '3 days' => '3 days',
                    '7 days' => '7 days',
                ]
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-select',
            ]
        ]);

        $this->add([
            'type' => Select::class,
            'name' => 'categoryId',
            'options' => [
                'label' => 'Poll Category',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
                'empty_option' => 'Select...',
                'value_options' => $this->categoriesTable->findAllCategories(),
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-select',
            ]
        ]);

        $this->add([
            'type' => Textarea::class,
            'name' => 'question',
            'options' => [
                'label' => 'Poll Question',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 300,
                'rows' => 3,
                'placeholder' => 'Ask a question...',
                'title' => 'Provide a poll question',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'options[]',
            'options' => [
                'label' => 'Poll Options',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 100,
                'placeholder' => 'Enter a possible option...',
                'title' => 'Provide a possible option',
                'data-bs-toggle' => 'tooltip',
                'data-bs-placement' => 'right',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => Button::class,
            'name' => 'addOption',
            'options' => [
                'label' => 'Add Another Option',
            ],
            'attributes' => [
                'class' => 'btn btn-outline-secondary btn-sm',
                'id' => 'addOption',
            ]
        ]);

        $this->add([
            'type' => Hidden::class,
            'name' => 'userId',
        ]);

        $this->add([
            'type' => Csrf::class,
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1440, 
                ],
            ],
        ]);

        $this->add([
            'type' => Submit::class,
            'name' => 'createPoll',
            'attributes' => [
                'value' => 'Create Poll',
                'class' => 'btn btn-primary btn-lg w-100'
            ]
        ]);
    }
}
