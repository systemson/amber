<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;

trait Relations
{
    public function hasAndBelongsToMany($class, $pivot)
    {
        $related = new $class;

        $query = $this->query();

        $pivot_id1 = $this->getResource() . '_' . $this->getId();
        $pivot_id2 = $related->getResource() . '_' . $related->getId();

        $query->removeCol('*');
        
        return $query
            ->cols([
                $related->getName() . '.*',
                $pivot . '.*'
            ])
            ->join('inner', $pivot, "{$pivot}.{$pivot_id1} = {$this->name}.{$this->id}")
            ->join('inner', $related->name, "{$pivot}.{$pivot_id2} = {$related->name}.{$related->id}")
        ;
    }

    public function hasOne(string $class, $pk = null, $fk = null)
    {
        $provider = new $class;

        $name = $provider->getName();

        $pk = $pk ?? $provider->getId();
        $fk = $fk ?? "{$name}_{$pk}";

        $this->relations[$provider->getName()] = [$pk => $fk];
    }

    public function belongsTo(string $class, string $fk = null, string $pk = null)
    {
        $related = new $class;

        $fk = $fk ?? $related->getResource() . '_' . $related->getId();
        $pk = $pk ?? $related->id;

        $this->query('select', true);

        $this
            ->from($related->getName())
            ->whereIn($pk, null)
        ;

        return (object) [
                'query' => $this->query,
                'provider' => $related,
                'pk' => $pk,
                'fk' => $fk,
                'multiple' => false,
            ]
        ;
    }

    public function hasMany(string $class, string $pk = null, string $fk = null)
    {
        $related = new $class;

        $fk = $fk ?? $this->getResource() . '_' . $this->getId();
        $pk = $pk ?? $this->id;
        $on = $this->id;

        $resource = $related->getResource();

        $query = $this->query('select', true);

        $this
            ->from($related->getName())
            ->whereIn($fk, null)
        ;

        return (object) [
                'query' => $this->query,
                'provider' => $related,
                'pk' => $fk,
                'fk' => $pk,
                'multiple' => true,
            ]
        ;
    }
}
