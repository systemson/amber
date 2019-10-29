<?php

namespace Amber\Model\QueryBuilder;

use Aura\SqlQuery\QueryFactory;
use Aura\SqlQuery\AbstractQuery;

class QueryBuilder
{
    use Insertable,
        Selectable,
        Updatable,
        Deletable
    ;

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

    public function __call($method, $args = [])
    {
        if (!$this->query instanceof AbstractQuery || !in_array($method, get_class_methods($this->query))) {
            throw new \Exception(sprintf(
                "Class %s doesn't have a method %s",
                get_called_class(),
                $method
            ));
        }

        $result = call_user_func_array([$this->query, $method], $args);

        if ($result instanceof AbstractQuery) {
            return $this;
        }

        return $result;
    }
}
