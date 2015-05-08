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
 * @since x.xx
 */
final class Issue
{
    /**
     * Error code for: word not found in any dictionary
     *
     * @since x.xx
     */
    const UNKNOWN_WORD = 'Unknown word';

    /**
     * Problem word
     *
     * @var string
     *
     * @since x.xx
     */
    public $word;

    /**
     * Error code (see class constants)
     *
     * @var string
     *
     * @since x.xx
     */
    public $code;

    /**
     * Suggested replacements
     *
     * @var string[]
     *
     * @since x.xx
     */
    public $suggestions = [];

    /**
     * Text line containing problem word
     *
     * @var int|null line number or null if not known
     * @since x.xx
     */
    public $line = null;

    /**
     * Problem word offset in the {@link $line}
     *
     * @var int|null offset in characters or null if not known
     * @since x.xx
     */
    public $offset = null;

    /**
     * Create new issue
     *
     * @param string $word problem word
     * @param string $code error code (see class constants)
     *
     * @since x.xx
     */
    public function __construct($word, $code = self::UNKNOWN_WORD)
    {
        $this->word = $word;
        $this->code = $code;
    }
}
