<?php

namespace Amber\Model\Mediator;

use PDO;
use Amber\Collection\Collection;
use Amber\Model\Resource\Resource;
use Aura\Sql\ExtendedPdo;
use Aura\Sql\Profiler\Profiler;
use Psr\Log\LoggerInterface;
use Amber\Phraser\Phraser;

class SqlMediator
{
    public $pdo;

    public function __construct(array $options = [])
    {
        $driver = $options['driver'];

        $dbname = $options['database'] ?? null;

        if ($driver == 'sqlite') {
            $this->pdo = new ExtendedPdo("sqlite:{$dbname}");
            return;
        }

        $host = $options['host'] ?? null;
        $port = $options['port'] ?? null;

        $user = $options['username'] ?? null;
        $pass = $options['password'] ?? null;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dsn = Phraser::make("{$driver}:")
            ->append("dbname={$dbname};", $dbname)
            ->append("host={$host};", $host)
            ->append("port={$port};", $port)
        ;

        $this->pdo = new ExtendedPdo((string) $dsn, $user, $pass, $options);
    }

    public function execute($query)
    {
        return $this->pdo->perform(
            $query->getStatement(),
            $query->getBindValues()
        );
    }

    public function select($query)
    {
        $sth = $this->execute($query);

        return $sth->fetchAll();
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
