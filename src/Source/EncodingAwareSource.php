<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source;

use Mekras\Speller\Exception\SourceException;

/**
 * Text source interface.
 *
 * @since 1.6
 *
 * @todo  Remove in version 3.0.
 */
interface EncodingAwareSource
{
    /**
     * Return source text encoding.
     *
     * @return string
     *
     * @since 1.6
     */
    public function getEncoding(): string;

    /**
     * Return text as one string.
     *
     * @return string
     *
     * @throws SourceException Fail to read from text source.
     * @since 1.6 Throws {@see SourceException}.
     * @since 1.0
     */
    public function getAsString(): string;
}
