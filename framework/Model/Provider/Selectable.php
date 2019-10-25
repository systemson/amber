<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;
use Amber\Container\Facades\QueryBuilder;

trait Selectable
{
    public function selectQuery(array $columns = [])
    {
        if (!is_null($this->query)) {
            return $this->query;
        }

        QueryBuilder::setLastInsertIdNames([
            $this->getName() . '.' . $this->getId() => $this->getName() . '_' . $this->getId() . '_seq',
        ]);

        return $this->query = QueryBuilder::newSelect($columns)
            ->from($this->getName())
        ;
    }

    public function select(array $columns = [])
    {
        $this->selectQuery($columns);

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
            ->where("{$column} NOT IN (?)", Gemstone::quote($array))
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

    /*public function page(int $page)
    {
        $this->setPaging($page);

        return $this;
    }*/

    /*public function limit(int $paging)
    {
        $this->query->paging = $paging;

        return $this;
    }*/

    public function count()
    {
        return $this
            ->select(["COUNT({$this->id})"])
            //->get()
        ;
    }
}
