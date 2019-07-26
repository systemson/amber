<?php

namespace Amber\Model\Provider;

use Amber\Model\Resource\Resource;
use Amber\Container\Facades\Gemstone;
use Carbon\Carbon;

trait Updatable
{
    public function update(Resource $resource)
    {
        if ($resource->isNew()) {
            return false;
        }

        $values = $resource->updatable();

        if ($values->isEmpty()) {
            return false;
        }

        if ($this->timestamps() && $this->editedAt()) {
            $resource->{static::EDITED_AT} = (string) Carbon::now();
        }

        $id = $resource->{$this->getId()};

        $query = $this->query('update')
            ->table($this->getName())
            ->cols($values->toArray())
            ->where($this->getId() . ' = ?', $id)
        ;

        $result = Gemstone::execute($query);

        if ($result === true) {
            return $resource->sync($this->find($id));
        }

        return $result;
    }
}
