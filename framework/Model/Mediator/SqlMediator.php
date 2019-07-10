<?php

namespace Amber\Model\Mediator;

use PDO;
use Amber\Collection\Collection;
use Amber\Model\Resource\Resource;
use Aura\Sql\ExtendedPdo;
use Aura\Sql\Profiler\Profiler;
use Psr\Log\LoggerInterface;

class SqlMediator
{
    protected $pdo;

    public function __construct(array $options = [])
    {
        $driver = getenv('DB_DRIVER');
        $name = getenv('DB_DATABASE');
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $user = getenv('DB_USERNAME');
        $pass = getenv('DB_PASSWORD');

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dsn = "{$driver}:dbname={$name};host={$host};port={$port}";

        $this->pdo = new ExtendedPdo($dsn, $user, $pass, $options, [], new Profiler());
    }

    protected function execute($query)
    {
        return $this->pdo->perform(
            $query->getStatement(),
            $query->getBindValues()
        );
    }

    public function select($query)
    {
        $sth = $this->execute($query);

        // get the results back as an associative array
        $sth->setFetchMode(
            PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE,
            Resource::class
        );

        if ($query->getLimit() === 1) {
            return $sth->fetch();
        }

        return new Collection($sth->fetchAll());
    }

    public function insert($query)
    {
        $result = $this->execute($query);

        $seq = $query->getLastInsertIdName('id');

        if ($result) {
            return $this->pdo->lastInsertId($seq);
        }

        return false;
    }

    public function update($query)
    {
        return $this->execute($query);
    }

    public function delete($query)
    {
        return $this->execute($query);
    }
}
