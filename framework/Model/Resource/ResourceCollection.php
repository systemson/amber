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
    public function join($array, string $name, string $fkey, string $pkey, bool $multiple = false): self
    {
        return $this->map(
            function ($item) use ($array, $name, $fkey, $pkey, $multiple) {
                foreach ($array as $value) {
                    if ($item->{$fkey} === $value->{$pkey}) {
                        if (!$multiple) {
                            $item->setRelation($name, $value);
                            break;
                        }

                        $values[] = $value;
                    }

                    $item->setRelation($name, $values);
                }
            }
        );
    }
}
