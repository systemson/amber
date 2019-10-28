<?php

namespace Amber\Model\QueryBuilder;

use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\AbstractQuery;

class QueryBuilder
{
    use Selectable;

    protected $query;
    protected $factory;

    public function __construct(QueryFactory $factory)
    {
        $this->factory = $factory;
    }

    public function setFactory(QueryFactory $factory): self
    {
        $this->factory = $factory;

        return $this;
    }

    public function getFactory(): QueryFactory
    {
        return $this->factory;
    }

    public function query(): AbstractQuery
    {
        return $this->query;
    }

    public function __call($method, $args = []): self
    {
        if (!$this->query instanceof AbstractQuery || !in_array($method, get_class_methods($this->query))) {
            throw new \Exception(sprintf(
                "Class %s doesn't have a method %s",
                get_called_class(),
                $method
            ));
        }

        $this->query = call_user_func_array([$this->query, $method], $args);

        return $this;
    }
}
