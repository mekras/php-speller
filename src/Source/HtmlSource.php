<?php
/**
 * PHP Speller
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source;

/**
 * HTML document as a text source.
 *
 * @since 1.5
 */
class HtmlSource implements Source
{
    /**
     * Attributes with user visible text contents.
     *
     * @var string[]
     */
    static private $textAttributes = [
        'abbr',
        'alt',
        'label',
        'placeholder',
        'title'
    ];

    /**
     * Source HTML.
     *
     * @var string
     */
    private $html;

    /**
     * Create text source from HTML.
     *
     * @param string $html
     *
     * @since 1.5
     */
    public function __construct($html)
    {
        $this->html = $html;
    }

    /**
     * Return text as one string.
     *
     * @return string
     *
     * @since 1.5
     */
    public function getAsString()
    {
        $document = new \DOMDocument('1.0');
        $document->loadHTML($this->html);

        return $this->extractFromNode($document->documentElement);
    }

    /**
     * Extract text from DOM node.
     *
     * @param \DOMNode $node
     *
     * @return string
     */
    private function extractFromNode(\DOMNode $node)
    {
        if ($node instanceof \DOMText) {
            return trim($node->textContent);
        }

        $text = [];

        if ($node instanceof \DOMElement) {
            foreach ($node->attributes as $attr) {
                /** @var \DOMAttr $attr */
                if (in_array($attr->name, self::$textAttributes, true)) {
                    $text[] = trim($attr->textContent);
                }
            }
            $text[] = $this->extractFromMeta($node);
            foreach ($node->childNodes as $child) {
                $text[] = $this->extractFromNode($child);
            }
        }

        return trim(implode(' ', $text));
    }

    /**
     * Extract text from meta tag.
     *
     * @param \DOMElement $node
     *
     * @return string
     */
    private function extractFromMeta(\DOMElement $node)
    {
        if (strtolower($node->nodeName) !== 'meta') {
            return '';
        }

        if (!($node->hasAttribute('name') && $node->hasAttribute('content'))) {
            return '';
        }

        if (!in_array(strtolower($node->getAttribute('name')), ['description', 'keywords'], true)) {
            return '';
        }

        return trim($node->getAttribute('content'));
    }
}
