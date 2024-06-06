<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Application\Model\Entity\OptionEntity;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Hydrator\ClassMethodsHydrator;

class OptionsTable extends AbstractTableGateway
{
    protected $table = 'poll_options';

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function findById(int $pollId): ?HydratingResultSet
    {
        $query = $this->sql->select()->where(['poll_id' => $pollId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);
        $result = $statement->execute();

        if (!$result) { return null; }

        $resultSet = new HydratingResultSet(new ClassMethodsHydrator(), new OptionEntity());
        $resultSet->initialize($result);

        return $resultSet;
    }

    public function insertOption(string $option, int $pollId): ResultInterface
    {
        $values = [
            'poll_id' => $pollId,
            'option' => $option,
        ];

        $query = $this->sql->insert()->values($values);
        $statement = $this->sql->prepareStatementForSqlObject($query);

        return $statement->execute();
    }

    public function updateVoteTally(int $optionId, int $pollId): ResultInterface
    {
        $query = $this->sql->update()->set(['vote_tally' => new Expression('vote_tally + 1')])
                    ->where(['option_id' => $optionId])
                    ->where(['poll_id' => $pollId]);
        
        $statement = $this->sql->prepareStatementForSqlObject($query);
        return $statement->execute();
    }
}
