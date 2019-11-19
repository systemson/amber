<?php

namespace Amber\Model\QueryBuilder;

use Aura\SqlQuery\QueryFactory as ParentFactory;

class QueryFactory extends ParentFactory
{
    /**
     *
     * Constructor.
     *
     * @param string $db The database type.
     *
     * @param string $common Pass the constant self::COMMON to force common
     * query objects instead of db-specific ones.
     *
     */
    public function __construct(
        $db,
        $common = null
    ) {
        if (!isset($this->quotes[$db])) {
            $db = self::COMMON;
        }

        $this->db = ucfirst(strtolower($db));
        $this->common = ($common === self::COMMON);
        $this->quote_name_prefix = $this->quotes[$this->db][0];
        $this->quote_name_suffix = $this->quotes[$this->db][1];
    }

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
