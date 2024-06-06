<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\I18n\Validator\IsInt;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Db\RecordExists;
use Laminas\Validator\NotEmpty;

class VotesTable extends AbstractTableGateway
{
    protected $table = 'poll_votes';

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function getVoteFormSanitizer(): InputFilter
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'optionId',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                    ['name' => ToInt::class]
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => IsInt::class],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => 'poll_options',
                            'field' => 'option_id',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'userId',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                    ['name' => ToInt::class]
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => IsInt::class],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => 'users',
                            'field' => 'user_id',
                            'adapter' => $this->adapter,
                        ]
                    ]
                ]
            ])
        );

        return $inputFilter;
    }

    public function hasVoted(int $pollId, int $userId): mixed
    {
        $query = $this->sql->select()->where(['poll_id' => $pollId])->where(['user_id' => $userId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);
        return $statement->execute()->current();
    }

    public function insertVote(array $data, int $pollId): ResultInterface
    {
        $values = [
            'poll_id' => $pollId,
            'user_id' => $data['userId'],
            'option_id' => $data['optionId'],
            'created' => date('Y-m-d H:i:s')
        ];

        $query = $this->sql->insert()->values($values);
        $statement = $this->sql->prepareStatementForSqlObject($query);

        return $statement->execute();
    }
}
