<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;
use Amber\Model\Resource\Resource;
use Carbon\Carbon;

trait Insertable
{
    public function insert(Resource $resource)
    {
        $resource->created_at = (string) Carbon::now();

        $values = $resource->insertable();

        if (empty($values)) {
            return false;
        }

        $query = $this->query('insert')
            ->into($this->getName())
            ->cols($values)
        ;

        $id = Gemstone::execute($query);

        if ($id !== false) {
            return $this->find($id);
        }

        return false;
    }
}
