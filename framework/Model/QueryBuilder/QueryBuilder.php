<?php

namespace Amber\Model\QueryBuilder;

use Aura\SqlQuery\QueryFactory;

class QueryBuilder extends QueryFactory
{
    /**
     *
     * Returns a new SELECT object.
     *
     * @return Common\SelectInterface
     *
     */
    public function newSelect($cols = [])
    {
        if (empty((array) $cols)) {
            $cols[] = '*';
        }

        return parent::newSelect()
            ->cols($cols)
        ;
    }
    /**
     *
     * Returns a new SELECT object.
     *
     * @return Common\SelectInterface
     *
     */
    public function newUpdate($cols = [])
    {
        if (empty((array) $cols)) {
            return parent::newUpdate();
        }

        return parent::newUpdate()
            ->cols($cols)
        ;
    }
}
