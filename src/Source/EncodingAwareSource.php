<?php
/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source;

/**
 * Text source interface.
 *
 * @since 1.6
 *
 * @todo  Merge with Source in version 2.0.
 * @todo  Remove in version 3.0.
 */
interface EncodingAwareSource extends Source
{
    /**
     * Return source text encoding.
     *
     * @return string
     *
     * @since 1.6
     */
    public function getEncoding();
}
