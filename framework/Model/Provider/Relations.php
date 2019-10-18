<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;
use Amber\Model\Provider\RelationProvider;

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

        foreach ($related->getAttributesNames() as $name) {
            $cols[] = $related->getName() . '.' . $name;
        }

        $cols[] = $pivot->getName() . '.' . $join1;
        $cols[] = $pivot->getName() . '.' . $join2;

        $this
            ->cols($cols)
            ->from($related->getName())
            ->whereIn($this->getName() . '.' . $fk, null)
            ->orderBy($related->getName() . '.' . $pk)
            ->join('inner', $pivot->getName(), $pivot->getName() . '.' . $join1 . ' = ' . $related->getName() . '.' . $pk)
            ->join('inner', $this->getName(), $this->getName() . '.' . $fk . ' = ' .  $pivot->getName() . '.' . $join2)
        ;

        $query = $this->query;
        $this->clearQuery();

        return new RelationProvider(
            $related,
            $query,
            $join2,
            $fk,
            true
        );
    }

    public function belongsTo(string $class, string $fk = null, string $pk = null)
    {
        $related = new $class;

        $fk = $fk ?? $related->getResource() . '_' . $related->getId();
        $pk = $pk ?? $related->id;

        $this->query('select', true);

        $this
            ->cols($related->getAttributesNames())
            ->from($related->getName())
            ->whereIn($pk, null)
            ->orderBy($related->id)
        ;

        $query = $this->query;
        $this->clearQuery();

        return new RelationProvider(
            $related,
            $query,
            $pk,
            $fk,
            true
        );
    }

    public function hasMany(string $class, string $fk = null, string $pk = null)
    {
        $related = new $class;

        $fk = $fk ?? $this->getResource() . '_' . $this->getId();
        $pk = $pk ?? $this->id;

        $resource = $related->getResource();

        $this->query('select', true);

        $this
            ->cols($related->getAttributesNames())
            ->from($related->getName())
            ->whereIn($fk, null)
            ->orderBy($related->id)
        ;

        $query = $this->query;
        $this->clearQuery();

        return new RelationProvider(
            $related,
            $query,
            $fk,
            $pk,
            true
        );
    }

    public function hasOne(string $class, string $fk = null, string $pk = null)
    {
        $related = new $class;

        $fk = $fk ?? $this->getResource() . '_' . $this->getId();
        $pk = $pk ?? $this->id;

        $resource = $related->getResource();

        $this->query('select', true);

        $this
            ->cols($related->getAttributesNames())
            ->from($related->getName())
            ->whereIn($fk, null)
            ->orderBy($related->id)
        ;

        $query = $this->query;
        $this->clearQuery();

        return new RelationProvider(
            $related,
            $query,
            $fk,
            $pk,
            true
        );
    }
}
