<?php
/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source;

use Mekras\Speller\Exception\SourceException;

/**
 * Text source interface.
 *
 * @deprecated since 1.6: use EncodingAwareSource for your own sources.
 *
 * @since      1.0
 */
interface Source
{
    /**
     * Return text as one string.
     *
     * @return string
     *
     * @throws SourceException Fail to read from text source.
     *
     * @since 1.6 Throws {@see SourceException}.
     * @since 1.0
     */
    public function getAsString();
}
