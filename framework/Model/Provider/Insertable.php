<?php

namespace Amber\Model\Provider;

use Amber\Model\Resource\Resource;
use Amber\Container\Facades\Gemstone;
use Carbon\Carbon;
use Amber\Collection\Collection;

trait Insertable
{
    public function insert(Resource $resource)
    {
        if (!$resource->isNew()) {
            return false;
        }

        $values = $resource->insertable();

        if ($values->isEmpty()) {
            $resource->setErrors(new Collection(['Nothing to insert.']));
            return false;
        }

        if ($this->timestamps() && $this->createdAt()) {
            $values->set(static::CREATED_AT, (string) Carbon::now());
        }

        $query = $this->query('insert')
            ->into($this->getName())
            ->cols($values->toArray())
        ;

        $id = Gemstone::execute($query);

        if ($id !== false) {
            return $resource->update($this->find($id)->toArray());
        }

        return false;
    }
}
