<?php
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
 * @since x.x
 */
interface PhpSpellerException
{
    /**
     * @return string
     */
    public function getMessage();
}
