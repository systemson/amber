<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;

trait Selectable
{
    public function select(array $columns = [])
    {
        $this->query()
            ->cols($columns)
        ;

        return $this;
    }

    public function all()
    {
        return $this->select()
            ->orderBy($this->getName() . '.' . $this->getId(), 'ASC')
            ->get()
        ;
    }

    public function first()
    {
        $this->query()
            ->limit(1)
        ;

        return $this->get();
    }

    public function last()
    {
        $this->query()
            ->orderBy([$this->getId(), 'DESC'])
            ->limit(1)
        ;

        return $this->get();
    }

    public function find($id)
    {
        return $this
            ->where($this->id, '=', $id)
            ->first()
        ;
    }

    public function where(string $column, string $operator, $value)
    {
        $this->query()
            ->where("{$column} {$operator} ?", $value)
        ;

        return $this;
    }

    public function whereNot(string $column, $value)
    {
        $this->query()
            ->where($column, '<>', $value)
        ;

        return $this;
    }

    public function whereIn(string $column, $values)
    {
        $this->query()
            ->where("{$column} IN (?)", Gemstone::quote($array))
        ;

        return $this;
    }

    public function whereNotIn(string $column, $values)
    {
        $this->query()
            ->where("{$column} NOT IN (?)", Gemstone::quote($array))
        ;

        return $this;
    }

    public function orderBy(string $column, string $order = 'ASC')
    {
        $this->query()
            ->orderBy(["{$column} $order"])
        ;

        return $this;
    }
}
