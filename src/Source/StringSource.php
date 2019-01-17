<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source;

/**
 * String as a text source.
 *
 * @since 1.6 Implements EncodingAwareSource.
 * @since 1.0
 */
class StringSource implements EncodingAwareSource
{
    /**
     * Source text.
     *
     * @var string
     */
    private $text;

    /**
     * Text encoding.
     *
     * @var string
     */
    private $encoding;

    /**
     * Create text source from string.
     *
     * @param string $text     Source text.
     * @param string $encoding Text encoding (default to "UTF-8").
     *
     * @since 1.6 New argument — $encoding.
     * @since 1.0
     */
    public function __construct(string $text, string $encoding = 'UTF-8')
    {
        $this->text = $text;
        $this->encoding = $encoding;
    }

    /**
     * Return text as one string
     *
     * @return string
     *
     * @since 1.0
     */
    public function getAsString(): string
    {
        return $this->text;
    }

    /**
     * Return source text encoding.
     *
     * @return string
     *
     * @since 1.6
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }
}
