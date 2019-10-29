<?php

namespace Amber\Model\QueryBuilder;

use Amber\Model\Resource\Resource;
use Amber\Container\Facades\Gemstone;
use Carbon\Carbon;
use Amber\Collection\Collection;

trait Insertable
{
    public function insert(): self
    {
        $this->query = $this
            ->getFactory()
            ->newInsert()
        ;

        return $this;
    }
}
