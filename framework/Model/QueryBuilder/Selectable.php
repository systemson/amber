<?php

namespace Amber\Model\QueryBuilder;

trait Selectable
{
    public function select(array $cols = []): self
    {
        $this->query = $this
            ->getFactory()
            ->newSelect()
            ->cols($cols)
            ;

        return $this;
    }

    public function where(string $column, string $operator, $value)
    {
        $this->query()
            ->where("{$column} {$operator} ?", $value)
        ;

        return $this;
    }

    public function whereAll(iterable $conditions = [])
    {
        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                $this->whereIn($column, $value);
                continue;
            }

            $this->where($column, '=', $value);
        }

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
            ->where("{$column} IN (?)", $values)
        ;

        return $this;
    }

    public function whereNotIn(string $column, $values)
    {
        $this->query()
            ->where("{$column} NOT IN (?)", $array)
        ;

        return $this;
    }

    public function orderBy(string $column = null, string $order = 'ASC')
    {
        if (!empty($column)) {
            $this->query()
                ->orderBy(["{$column} $order"])
            ;
        }

        return $this;
    }

    public function count(string $column = '*')
    {
        if ($this->query == null) {
            return $this
                ->select(["COUNT({$column})"])
            ;
        }

        $this->query
            ->resetCols()
            ->cols(["COUNT({$column})"])
        ;

        return $this;
    }
}
