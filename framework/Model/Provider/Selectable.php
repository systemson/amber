<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;

trait Selectable
{
    public function select(array $columns = ['*'])
    {
        $this->query('select')
            ->from($this->getName())
            ->cols($columns)
        ;

        return $this;
    }

    public function all()
    {
        return $this->select()
            ->get()
        ;
    }

    public function first()
    {
        return $this->select()
            ->limit(1)
            ->get()
        ;
    }

    public function find($id)
    {
        return $this->bootResource($this
            ->where($this->id, '=', $id)
            ->limit(1)
            ->get()
        );
    }

    public function where(string $column, string $operator, $value)
    {
        $this->select();

        $this->query
            ->where("{$column} {$operator} ?", $value)
        ;

        return $this;
    }

    public function whereNot(string $column, $value)
    {
        $this->select()
            ->where($column, '<>', $value)
        ;

        return $this;
    }

    public function whereIn(string $column, $values)
    {
        $this->select();

        $pdo = '';

        $this->query
            ->where("{$column} IN (?)", Gemstone::quote($array))
        ;

        return $this;
    }

    public function whereNotIn(string $column, $values)
    {
        $this->select();

        $this->query
            ->where("{$column} NOT IN (?)", Gemstone::quote($array))
        ;

        return $this;
    }
}
