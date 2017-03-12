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
 * @since x.x derived from MetaSource.
 * @since 1.6 derived from StringSource.
 * @since 1.5
 */
class HtmlSource extends MetaSource
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
     * Create text source from HTML.
     *
     * @param EncodingAwareSource|string $source
     *
     * @throws \Mekras\Speller\Exception\SourceException
     *
     * @since x.x Accepts EncodingAwareSource
     * @since 1.5
     *
     * @todo  deprecate string $source in version 2.0
     */
    public function __construct($source)
    {
        if (!$source instanceof EncodingAwareSource) {
            $source = new StringSource($source);
        }
        parent::__construct($source);
        $html = $this->source->getAsString();
        $document = new \DOMDocument('1.0');
        $document->loadHTML($html);
        $this->encoding = $document->encoding;
        $filter = new HtmlFilter();
        $this->text = $filter->filter($html);
    }

    /**
     * Return text as one string
     *
     * @return string
     *
     * @since 1.0
     */
    public function getAsString()
    {
        return (string) $this->text;
    }

    /**
     * Return source text encoding.
     *
     * @return string
     *
     * @since x.x
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
}
