<?php

namespace Amber\Model\Mediator;

use PDO;
use Amber\Collection\Collection;

class PgsqlMediator
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

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    protected function execute($query)
    {
        $sth = $this->pdo->prepare($query->getStatement());

        // bind the values and execute
        $sth->execute($query->getBindValues());

        return $sth;

    }

    public function select($query)
    {
        $sth = $this->execute($query);

        // get the results back as an associative array
        $sth->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, Collection::class);

        if ($query->getLimit() === 1) {
            return $sth->fetch();
        }

        return new Collection($sth->fetchAll());
    }

    public function insert($query)
    {
        $result = $this->execute($query);

        if ($result) {
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    public function update($query)
    {
        return $this->execute($query);
    }
}
