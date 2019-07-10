<?php

namespace Amber\Model;

use PDO;
use Aura\Sql\ExtendedPdo;

trait ConnectionManager
{
    protected $connections;

    public function addConnection(string $name, array $config = []): self
    {
        $this->connections[$name] = $config;

        return $this;
    }

    public function hasConnection(string $name): bool
    {
        return isset($this->connections[$name]);
    }

    public function getConnection(string $name = null): array
    {
        if (is_null($name)) {
            return $this->connections['default'];
        }
        
        return $this->connections[$name];
    }

    public function connection(string $name = null): PDO
    {
        if (!is_null($name) && $this->hasConnection($name)) {
            $configs = $this->getConnection($name);
        } else {
            $configs = $this->getConnection('default');
        }

        return new ExtendedPdo($this->getDsnFromArray($configs), $configs['user'], $configs['password']);
    }

    protected function getDsnFromArray(array $configs = []): string
    {
        $driver = $configs['driver'];
        $dbname = $configs['dbname'];
        $host = $configs['host'];
        $port = $configs['port'];

        return "{$driver}:dbname={$dbname};host={$host};port={$port}";
    }
}
