<?php

namespace Amber\Model\Provider;

use Amber\Model\Resource\Resource;
use Amber\Container\Facades\Gemstone;
use Carbon\Carbon;

trait Updatable
{
    public function update(Resource $resource)
    {
        $resource->updated_at = (string) Carbon::now();

        $values = $resource->updatable();

        if (empty($values)) {
            return false;
        }

        $id = $resource->{$this->getId()};

        $query = $this->query('update')
            ->table($this->getName())
            ->cols($values)
            ->where($this->getId() . ' = ?', $id)
        ;

        $result = Gemstone::execute($query);

        if ($result === true) {
            return $this->find($id);
        }

        return $result;
    }
}
