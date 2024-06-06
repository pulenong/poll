<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Application\Model\Entity\PollEntity;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\I18n\Validator\IsInt;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Db\RecordExists;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

class PollsTable extends AbstractTableGateway
{
    protected $table = 'polls';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function closePoll(int $pollId): ResultInterface
    {
        $query = $this->sql->update()->set(['status' => '0'])->where(['poll_id' => $pollId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);
        return $statement->execute();
    }

    public function findById(int $pollId): ?PollEntity
    {
        $query = $this->sql->select()->join('poll_categories', 'poll_categories.category_id='.$this->table.'.category_id', 'category')
                       ->join('users', 'users.user_id='.$this->table.'.user_id', ['username', 'picture'])
                       ->where([$this->table.'.poll_id' => $pollId]);
        
        $statement = $this->sql->prepareStatementForSqlObject($query);
        $result = $statement->execute()->current();

        if (!$result) { return null; }

        $entity = new PollEntity();
        (new ClassMethodsHydrator())->hydrate($result, $entity);

        return $entity;
    }

    public function findByOwnerId(int $userId): ?HydratingResultSet
    {
        $query = $this->sql->select()
                  ->join('poll_categories', 'poll_categories.category_id='.$this->table.'.category_id', 'category')
                  ->join('users', 'users.user_id='.$this->table.'.user_id', ['username', 'picture'])
                  ->where([$this->table.'.user_id' => $userId]);
        
        $statement = $this->sql->prepareStatementForSqlObject($query);
        $result = $statement->execute();

        if (!$result) { return null; }

        $resultSet = new HydratingResultSet(new ClassMethodsHydrator(), new PollEntity());
        $resultSet->initialize($result);

        return $resultSet;
    }

    public function getCreateFormSanitizer(): InputFilter
    {
        $inputFilter = new InputFilter();
        $factory = new Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'title',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 2,
                            'max' => 100,
                            'messages' => [
                                StringLength::TOO_SHORT => 'Poll title must have at least 2 characters',
                                StringLength::TOO_LONG => 'Poll title must have at most 100 characters',
                            ],
                        ],
                    ],
                ]
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'categoryId',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                    ['name' => ToInt::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => IsInt::class],
                    [
                        'name' => RecordExists::class,
                        'options' => [
                            'table' => 'poll_categories',
                            'field' => 'category_id',
                            'adapter' => $this->adapter,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'timeout',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => InArray::class,
                        'options' => [
                            'haystack' => ['1 day', '3 days', '7 days'],
                            'strict' => InArray::COMPARE_NOT_STRICT,
                        ],
                    ],
                ],
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'question',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 4,
                            'max' => 300,
                            'messages' => [
                                StringLength::TOO_SHORT => 'Poll question must have at least 4 characters',
                                StringLength::TOO_LONG => 'Poll question must have at most 300 characters',
                            ],
                        ],
                    ],
                ]
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'userId',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                    ['name' => ToInt::class],
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
                        ],
                    ],
                ],
            ])
        );

        return $inputFilter;
    }

    public function insertPoll(array $data): ResultInterface
    {
        $values = [
            'user_id' => $data['userId'],
            'category_id' => $data['categoryId'],
            'title' => $data['title'],
            'question' => $data['question'],
            'timeout' => date('Y-m-d H:i:s', strtotime('+'.$data['timeout'])),
            'created' => date('Y-m-d H:i:s')
        ];

        $query = $this->sql->insert()->values($values);
        $statement = $this->sql->prepareStatementForSqlObject($query);

        return $statement->execute();
    }

    public function updateTotalVotes(int $pollId): ResultInterface
    {
        $query = $this->sql->update()->set(['total_votes' => new Expression('total_votes + 1')])->where(['poll_id' => $pollId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);
        return $statement->execute();
    }
}
