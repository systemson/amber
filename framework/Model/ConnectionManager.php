<?php

namespace Amber\Model;

use PDO;
use Aura\Sql\ExtendedPdo;

trait ConnectionManager
{
    protected $connections;

    public function addConnection(string $name, array $configs = []): self
    {
        $this->connections[$name] = $configs;

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

    public function addConnections(array $connections): self
    {
        foreach ($connections as $name => $configs) {
            $this->addConnection($name, $configs);
        }

        return $this;
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
        $dbname = $configs['database'];
        $host = $configs['host'];
        $port = $configs['port'];

        return "{$driver}:dbname={$dbname};host={$host};port={$port}";
    }
}
