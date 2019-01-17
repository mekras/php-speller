<?php
declare(strict_types=1);

/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source\Filter;

/**
 * Filter interface.
 *
 * Filters are used to filter out text, which does not require checking.
 *
 * @since 1.2
 */
interface Filter
{
    /**
     * Filter string.
     *
     * @param string $string String to be filtered.
     *
     * @return string Filtered string.
     *
     * @since 1.2
     */
    public function filter(string $string): string;
}
