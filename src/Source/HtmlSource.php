<?php

/**
 * PHP Speller
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mekras\Speller\Source;

use Mekras\Speller\Exception\SourceException;
use Mekras\Speller\Source\Filter\HtmlFilter;

/**
 * HTML document as a text source.
 *
 * @since 1.6 derived from MetaSource.
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
     * @throws SourceException
     *
     * @since 1.6 Accepts EncodingAwareSource
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
        $document = $this->createDomDocument($html);
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

    /**
     * Create DOMDocument from HTML string.
     *
     * @param string $html
     *
     * @return \DOMDocument
     *
     * @throws SourceException On invalid HTML.
     *
     * @since 1.7
     */
    protected function createDomDocument($html): \DOMDocument
    {
        $document = new \DOMDocument('1.0');
        $previousValue = libxml_use_internal_errors(true);
        libxml_clear_errors();
        $document->loadHTML($html);
        /** @var \LibXMLError[] $errors */
        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previousValue);

        foreach ($errors as $error) {
            if (LIBXML_ERR_ERROR === $error->level || LIBXML_ERR_FATAL === $error->level) {
                throw new SourceException(
                    sprintf('%s at %d:%d', trim($error->message), $error->line, $error->column),
                    $error->code
                );
            }
        }

        return $document;
    }
}
