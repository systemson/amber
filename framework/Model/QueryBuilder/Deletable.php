<?php

namespace Amber\Model\QueryBuilder;

use Amber\Container\Facades\Gemstone;
use Amber\Model\Resource\Resource;

trait Deletable
{
    public function delete(): self
    {
        $this->query = $this
            ->getFactory()
            ->newDelete()
        ;

        return $this;
    }
}
