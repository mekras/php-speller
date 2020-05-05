<?php

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mekras\Speller;

/**
 * Representing any dictionary for a certain speller
 */
class Dictionary
{
    /** @var string */
    private $dictionaryPath;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->dictionaryPath = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->dictionaryPath;
    }
}
