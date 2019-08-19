<?php

namespace Amber\Model\Resource;

use Amber\Collection\Collection;
use Amber\Collection\Base\StatementsTrait;

/**
 * @todo MUST extend from Typed Collection when available.
 */
class ResourceCollection extends Collection implements ResourceCollectionInterface
{
    /**
     * Returns a new Collection joined by the specified column.
     *
     * @param array  $array The array to merge
     * @param string $pkey  The key to compare on the current collection.
     * @param string $fkey  The key to compare on the provided array.
     *
     * @return CollectionInterface A new collection instance.
     */
    public function join(array $array, string $name, string $pkey, string $fkey): self
    {
        return $this->map(
            function ($item) use ($array, $name, $pkey, $fkey) {
                foreach ($array as $value) {
                    if ($item->{$pkey} === $value[$fkey]) {
                        $item->setRelation($name, $value);
                    }
                }
            }
        );
    }
}
