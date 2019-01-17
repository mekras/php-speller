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
 * Filter which strips all input text
 *
 * All characters except new lines (\n), tabs (\t) and spaces will be replaces with spaces.
 *
 * @since 1.2
 */
class StripAllFilter implements Filter
{
    /**
     * Filter string
     *
     * @param string $string string to be filtered
     *
     * @return string filtered string
     *
     * @since 1.2
     */
    public function filter(string $string): string
    {
        return preg_replace('/\S/', ' ', $string);
    }
}
