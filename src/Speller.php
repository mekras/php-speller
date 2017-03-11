<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller;

use Mekras\Speller\Source\Source;

/**
 * Speller interface.
 *
 * @since 1.0
 */
interface Speller
{
    /**
     * Check text.
     *
     * Check given text and return an array of spelling issues.
     *
     * @param Source $source    Text source to check.
     * @param array  $languages List of languages used in text (IETF language tag).
     *
     * @return Issue[]
     *
     * @link  http://tools.ietf.org/html/bcp47
     * @since 1.0
     */
    public function checkText(Source $source, array $languages);

    /**
     * Return list of supported languages.
     *
     * @return string[]
     *
     * @since 1.0
     */
    public function getSupportedLanguages();
}
