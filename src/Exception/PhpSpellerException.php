<?php

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mekras\Speller\Exception;

use Throwable;

/**
 * Common interface for all Speller exceptions.
 *
 * @since 1.6
 */
interface PhpSpellerException extends Throwable
{
}
