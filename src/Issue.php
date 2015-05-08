<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller;

/**
 * Spelling issue
 *
 * @since 1.00
 */
final class Issue
{
    /**
     * Error code for: word not found in any dictionary
     *
     * @since 1.00
     */
    const UNKNOWN_WORD = 'Unknown word';

    /**
     * Problem word
     *
     * @var string
     *
     * @since 1.00
     */
    public $word;

    /**
     * Error code (see class constants)
     *
     * @var string
     *
     * @since 1.00
     */
    public $code;

    /**
     * Suggested replacements
     *
     * @var string[]
     *
     * @since 1.00
     */
    public $suggestions = [];

    /**
     * Text line containing problem word
     *
     * @var int|null line number or null if not known
     * @since 1.00
     */
    public $line = null;

    /**
     * Problem word offset in the {@link $line}
     *
     * @var int|null offset in characters or null if not known
     * @since 1.00
     */
    public $offset = null;

    /**
     * Create new issue
     *
     * @param string $word problem word
     * @param string $code error code (see class constants)
     *
     * @since 1.00
     */
    public function __construct($word, $code = self::UNKNOWN_WORD)
    {
        $this->word = $word;
        $this->code = $code;
    }
}
