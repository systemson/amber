<?php

namespace Amber\Model\Provider;

use Amber\Collection\Collection;
use Amber\Container\Facades\Gemstone;
use Amber\Container\Facades\QueryBuilder;
use Amber\Model\Resource\ResourceCollection;

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

    public function first($query)
    {
        $result = current(Gemstone::execute($query));

        if ($result) {
            return $this->pushRelations($this->new($result, true), $result);
        }

        return null;
    }

    public function execute($query)
    {
        return Gemstone::execute($query);
    }

    public function get($query)
    {
        $result = new ResourceCollection($this->execute($query));

        if ($result->isEmpty()) {
            return $result;
        }

        $new = $result->map(function ($values) {
            return $this->new($values, true);
        });

        return $this->pushRelations($new, $result);
    }

    public function pushRelations($new, $result)
    {
        foreach ($this->relations as $name) {
            $relation = $this->{$name}();

            if ($result instanceof ResourceCollection && $result->isNotEmpty()) {
                $bindValues = array_unique($new->map(function ($resource) use ($relation) {
                    return $resource->{$relation->getFkey()};
                })
                    ->toArray())
                ;
            } else {
                $bindValues = $new->{$relation->getFkey()};
            }

            $query = $relation->getQuery()->bindValue('_1_', $bindValues);

            $this->eagerLoadedRelations[$name] = $relation;

            $relationResult = new Collection($this->execute($query));

            if ($relationResult->isNotEmpty()) {
                $relationResult = $relationResult;

                $relationResult = $relationResult->map(function ($values) use ($relation, $name) {

                    if (!is_array($values)) {
                        $values = $values->toArray();
                    }

                    $item =  $relation->getProvider()->new($values, true);

                    $item->setMetadata($name, [
                        $relation->getPkey() => $values[$relation->getPkey()],
                        $relation->getFkey() => $values[$relation->getFkey()] ?? null,
                    ]);

                    return $item;
                });
            }

            $new->join(
                $relationResult,
                $name,
                $relation->getFkey(),
                $relation->getPkey(),
                $relation->getMultiple()
            );
        }

        return $new;
    }
}
