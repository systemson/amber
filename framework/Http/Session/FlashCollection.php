<?php
/**
 * This file is part of the Amber/Collection package.
 *
 * @package Amber/Collection
 * @author  Deivi Peña <systemson@gmail.com>
 * @license GPL-3.0-or-later
 * @license https://opensource.org/licenses/gpl-license GNU Public License
 */

namespace Amber\Http\Session;

use Amber\Collection\ImmutableCollection;
use Carbon\Carbon;

/**
 *
 */
class FlashCollection extends ImmutableCollection
{
    const FLASH_NAME = '_amber_flash';

    public function __construct()
    {
        $flash = $_SESSION[static::FLASH_NAME] ?? [];
        unset($_SESSION[static::FLASH_NAME]);

        parent::__construct($flash);
    }

    public function offsetSet($offset, $value)
    {
        $_SESSION[static::FLASH_NAME][$offset] = $value;
    }
}
