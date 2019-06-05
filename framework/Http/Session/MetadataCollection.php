<?php
/**
 * This file is part of the Amber/Collection package.
 *
 * @package Amber/Collection
 * @author  Deivi Peña <systemson@gmail.com>
 * @license GPL-3.0-or-later
 * @license https://opensource.org/licenses/gpl-license GNU Public License
 */

namespace Amber\Framework\Http\Session;

use Amber\Collection\ImmutableCollection;
use Carbon\Carbon;

/**
 *
 */
class MetadataCollection extends ImmutableCollection
{
    const METADATA_NAME = '_metadata';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function __construct()
    {
        $array = $this->init($_SESSION[MetadataCollection::METADATA_NAME] ?? []);
        parent::__construct($array);
    }

    protected function init(array $array)
    {
        if (!isset($array[static::CREATED_AT])) {
            $array[static::CREATED_AT] = $_SESSION[static::METADATA_NAME][static::CREATED_AT] = $this->now();
            $array[static::UPDATED_AT] = $_SESSION[static::METADATA_NAME][static::UPDATED_AT] = $this->now();
        }
        return $array;
    }

    protected function now(): string
    {
        //date('Y-m-d H:i:s');
        return (string) Carbon::now();
    }

    public function touch()
    {
        $_SESSION[static::METADATA_NAME][static::UPDATED_AT] = $this->now();
    }
}
