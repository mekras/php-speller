<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Exception;

/**
 * Common interface for all Speller exceptions.
 *
 * @since 1.6
 */
interface PhpSpellerException
{
    /**
     * @return string
     */
    public function getMessage();
}
