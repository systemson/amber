<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;
use Amber\Model\Resource\Resource;

trait Deletable
{
    public function delete(Resource $resource)
    {
        $query = $this->query('delete')
            ->from($this->getName())
            ->where($this->getId() . ' = ?', $resource->{$this->getId()})
        ;

        $resource->clear();

        return Gemstone::execute($query);
    }
}
