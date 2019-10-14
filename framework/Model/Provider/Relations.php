<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;

trait Relations
{
    public function hasAndBelongsToMany(string $class, string $pivot)
    {
        $related = new $class;
        $pivot = new $pivot;

        $join1 = $fk ?? $related->getResource() . '_' . $related->getId();
        $join2 = $fk ?? $this->getResource() . '_' . $related->getId();
        $pk = $pk ?? $related->id;
        $fk = $fk ?? $this->id;

        $this->query('select', true);

        $this
            ->cols([
                $related->getName() . '.*',
                $pivot->getName() . '.' . $join1,
                $pivot->getName() . '.' . $join2,
            ])
            ->from($related->getName())
            ->whereIn($this->getName() . '.' . $fk, null)
            ->orderBy($related->getName() . '.' . $pk)
            ->join('inner', $pivot->getName(), $pivot->getName() . '.' . $join1 . ' = ' . $related->getName() . '.' . $pk)
            ->join('inner', $this->getName(), $this->getName() . '.' . $fk . ' = ' .  $pivot->getName() . '.' . $join2)
        ;
        $query = $this->query; // dd($query->getStatement());
        $this->clearQuery();

        return (object) [
                'query' => $query,
                'provider' => $related,
                'pk' => $join2,
                'fk' => $fk,
                'multiple' => true,
            ]
        ;
    }

    public function belongsTo(string $class, string $fk = null, string $pk = null)
    {
        $related = new $class;

        $fk = $fk ?? $related->getResource() . '_' . $related->getId();
        $pk = $pk ?? $related->id;

        $this->query('select', true);

        $this
            ->cols(['*'])
            ->from($related->getName())
            ->whereIn($pk, null)
            ->orderBy($related->id)
        ;

        $query = $this->query;
        $this->clearQuery();

        return (object) [
                'query' => $query,
                'provider' => $related,
                'pk' => $pk,
                'fk' => $fk,
                'multiple' => false,
            ]
        ;
    }

    public function hasMany(string $class, string $fk = null, string $pk = null)
    {
        $related = new $class;

        $fk = $fk ?? $this->getResource() . '_' . $this->getId();
        $pk = $pk ?? $this->id;

        $resource = $related->getResource();

        $this->query('select', true);

        $this
            ->cols(['*'])
            ->from($related->getName())
            ->whereIn($fk, null)
            ->orderBy($related->id)
        ;

        $query = $this->query;
        $this->clearQuery();

        return (object) [
                'query' => $query,
                'provider' => $related,
                'pk' => $fk,
                'fk' => $pk,
                'multiple' => true,
            ]
        ;
    }

    public function hasOne(string $class, string $fk = null, string $pk = null)
    {
        $related = new $class;

        $fk = $fk ?? $this->getResource() . '_' . $this->getId();
        $pk = $pk ?? $this->id;

        $resource = $related->getResource();

        $this->query('select', true);

        $this
            ->cols(['*'])
            ->from($related->getName())
            ->whereIn($fk, null)
            ->orderBy($related->id)
        ;

        $query = $this->query;
        $this->clearQuery();

        return (object) [
                'query' => $query,
                'provider' => $related,
                'pk' => $fk,
                'fk' => $pk,
                'multiple' => false,
            ]
        ;
    }
}
