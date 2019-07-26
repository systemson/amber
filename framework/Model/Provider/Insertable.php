<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;
use Amber\Model\Resource\Resource;
use Carbon\Carbon;

trait Insertable
{
    public function insert(Resource $resource)
    {
        if (!$resource->isNew()) {
            return false;
        }

        $values = $resource->insertable();

        if ($values->isEmpty()) {
            return false;
        }

        if ($this->timestamps() && $this->createdAt()) {
            $resource->{static::CREATED_AT} = (string) Carbon::now();
        }

        $query = $this->query('insert')
            ->into($this->getName())
            ->cols($values->toArray())
        ;

        $id = Gemstone::execute($query);

        if ($id !== false) {
            return $resource->sync($this->find($id));
        }

        return false;
    }
}
