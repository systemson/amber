<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;
use Amber\Container\Facades\QueryBuilder;

trait Selectable
{
    public function all()
    {
        $query = $this->query()
            ->select()
            ->orderBy($this->getName() . '.' . $this->getId(), 'ASC')
            ->get()
        ;

        return $this->get($query);
    }

    public function first($query)
    {
        $query
            ->limit(1)
        ;

        return $this->get($query);
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
        $query = $this->query()
            ->select(['*'])
            ->where($this->id, '=', $id)
            ->from($this->getName())
        ;

        return $this->first($query);
    }

    public function count()
    {
        $query = $this->query()
            ->select("COUNT({$this->id})")
            ->from($this->getName())
        ;

        return $this->get($query);
    }
}
