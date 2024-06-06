<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\AbstractTableGateway;

class CategoriesTable extends AbstractTableGateway
{
    protected $table = 'poll_categories';

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function findAllCategories(): array
    {
        $query = $this->sql->select()->order('category ASC');
        $statement = $this->sql->prepareStatementForSqlObject($query);
        $result = $statement->execute();

        $row = [];
        foreach ($result as $column) {
            $row[$column['category_id']] = $column['category'];
        }

        return $row;
    }
}
