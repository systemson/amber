<?php

namespace Amber\Model\QueryBuilder;

use Amber\Model\Resource\Resource;
use Amber\Container\Facades\Gemstone;
use Carbon\Carbon;
use Amber\Collection\Collection;

trait Updatable
{
    public function update(): self
    {
        $this->query = $this
            ->getFactory()
            ->newUpdate()
        ;

        return $this;
    }
}
