<?php
/**
 * PHP Speller
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source;

use Mekras\Speller\Source\Filter\HtmlFilter;

/**
 * HTML document as a text source.
 *
 * @since 1.6 derived from StringSource.
 * @since 1.5
 */
class HtmlSource extends StringSource
{
    /**
     * Create text source from HTML.
     *
     * @param string $html
     *
     * @since 1.5
     */
    public function __construct($html)
    {
        $document = new \DOMDocument('1.0');
        $document->loadHTML($html);
        $filter = new HtmlFilter();
        parent::__construct($filter->filter($html), $document->encoding);
    }
}
