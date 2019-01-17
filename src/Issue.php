<?php
declare(strict_types=1);

/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller;

/**
 * Spelling issue
 *
 * @since 1.0
 */
final class Issue
{
    /**
     * Error code for: word not found in any dictionary
     *
     * @since 1.0
     */
    public const UNKNOWN_WORD = 'Unknown word';

    /**
     * Problem word
     *
     * @var string
     *
     * @since 1.0
     */
    public $word;

    /**
     * Error code (see class constants)
     *
     * @var string
     *
     * @since 1.0
     */
    public $code;

    /**
     * Suggested replacements
     *
     * @var string[]
     *
     * @since 1.0
     */
    public $suggestions = [];

    /**
     * Text line containing problem word
     *
     * @var int|null line number or null if not known
     * @since 1.0
     */
    public $line;

    /**
     * Problem word offset in the {@link $line}
     *
     * @var int|null offset in characters or null if not known
     * @since 1.0
     */
    public $offset;

    /**
     * Create new issue
     *
     * @param string $word problem word
     * @param string $code error code (see class constants)
     *
     * @since 1.0
     */
    public function __construct(string $word, string $code = self::UNKNOWN_WORD)
    {
        $this->word = $word;
        $this->code = $code;
    }
}
