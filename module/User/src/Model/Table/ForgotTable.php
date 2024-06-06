<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\AbstractTableGateway;
use LengthException;

class ForgotTable extends AbstractTableGateway
{
    /** @var string|array|TableIdentifier */
    protected $table = 'user_forgot';

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * Delete tokens older than 12hrs
     *
     * @return ResultInterface
     */
    public function deleteOldTokens(): ResultInterface
    {
        $query = $this->sql->delete()->where(['created < ?' => date('Y-m-d H:i:s', time() - (3600 * 12))]);
        $statement = $this->sql->prepareStatementForSqlObject($query);
        return $statement->execute();
    }

    /**
     * Delete token belonging to $userId specified
     *
     * @param int $userId
     * @return ResultInterface
     */
    public function deleteToken(int $userId): ResultInterface
    {
        $query = $this->sql->delete()->where(['user_id' => $userId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);
        return $statement->execute();
    }

    /**
     * Find token matching $token and $userId provided
     *
     * @param string $token
     * @param int $userId
     * @return mixed
     */
    public function findToken(string $token, int $userId): mixed
    {
        $query = $this->sql->select()->where(['token' => $token])->where(['user_id' => $userId]);
        $statement = $this->sql->prepareStatementForSqlObject($query);
        return $statement->execute()->current();
    }

    /**
     * Generate a token
     *
     * @param int $length
     * @return string
     */
    public function generateToken(int $length): string
    {
        if ($length < 8 || $length > 40) {
            throw new LengthException('Parameter must be a number between 8 and 40.');
        }

        $chars = '0aNbOc1PdQeR2fSgTh3UiVjW4kXlYm5XnAoB6pCqDr7EsFtG8uHvIw9JxKyLzM';
        $token = '';

        for ($i = 0; $i < $length; $i++) { 
            $random = mt_rand(0, mb_strlen($chars) - 1);
            $token .= mb_substr($chars, $random, 1);
        }

        return $token;
    }

    /**
     * Save $token belonging to $userId
     *
     * @param string $token
     * @param int $userId
     * @return ResultInterface
     */
    public function insertToken(string $token, int $userId): ResultInterface
    {
        $values = [
            'user_id' => $userId,
            'token' => $token,
            'created' => date('Y-m-d H:i:s')
        ];

        $query = $this->sql->insert()->values($values);
        $statement = $this->sql->prepareStatementForSqlObject($query);
        return $statement->execute();
    }
}
