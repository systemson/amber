<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;

trait Relations
{
    public function with(array $relations = [])
    {
        foreach ($relations as $relation) {
            $callback = [$this, $relation];

            $this->eagerLoadedRelations[$relation] = Gemstone::execute(call_user_func($callback));
            $this->clearQuery();
        }

        return $this;
    }

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

    public function hasMany(string $class, $pk = null, $fk = null)
    {
        $provider = new $class;

        $name = $provider->getName();
        $pk = $pk ?? $this->getId();
        $fk = $fk ?? "{$this->getResource()}_{$pk}";

        return $this->select()
            ->join('inner', $name, "{$name}.{$fk} = {$this->getName()}.{$pk}")
            ->get()
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

    public function belongsTo(string $class, $pk = null, $fk = null)
    {
        $provider = new $class;

        $name = $provider->getName();

        $pk = $pk ?? $provider->getId();
        $fk = $fk ?? "{$name}_{$pk}";

        $this->relations[$provider->getName()] = [$pk => $fk];
    }
}
