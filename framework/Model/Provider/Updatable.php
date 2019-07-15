<?php

namespace Amber\Model\Provider;

use Amber\Model\Resource\Resource;
use Amber\Container\Facades\Gemstone;

trait Updatable
{
    public function update(Resource $resource)
    {
        $values = $resource->updatable();

        if (empty($values)) {
            return false;
        }

        $query = $this->query('update')
            ->table($this->getName())
            ->cols($values)
            ->where($this->getId() . ' = ?', $resource->{$this->getId()})
        ;

        $result = Gemstone::execute($query);

        if ($result === true) {
            $resource->init();
        }

        return $result;
    }
}
