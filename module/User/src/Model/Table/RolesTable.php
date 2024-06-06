<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\AbstractTableGateway;

class RolesTable extends AbstractTableGateway
{
    /** @var string|array|TableIdentifier */
    protected $table = 'user_roles';

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
}
