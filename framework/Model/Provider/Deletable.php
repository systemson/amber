<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;

trait Deletable
{

    public function delete(Resource $resource)
    {
        $query = $this->query()
            ->from($this->getName())
            ->where($this->getId() . ' = ?', $resource->{$this->getId()})
        ;

        return Gemstone::execute($query);

        return false;
    }
}
