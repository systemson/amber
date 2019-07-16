<?php

namespace Amber\Container\Facades;

use Amber\Container\ContainerFacade;
use Amber\Model\QueryBuilder\QueryBuilder as Accessor;
use Amber\Utils\Traits\SingletonTrait;

class QueryBuilder extends ContainerFacade
{
    use SingletonTrait;

    /**
     * @var string The class accessor.
     */
    protected static $accessor = Accessor::class;
}
