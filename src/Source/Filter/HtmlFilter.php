<?php
declare(strict_types=1);

/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source\Filter;

/**
 * Filter replaces HTML tags with spaces.
 *
 * @since 1.3
 */
class HtmlFilter implements Filter
{
    /**
     * Attribute name context.
     */
    public const CTX_ATTR_NAME = 'attr_name';

    /**
     * Attribute value context.
     */
    public const CTX_ATTR_VALUE = 'attr_value';

    /**
     * Tag attributes context.
     */
    public const CTX_TAG_ATTRS = 'tag_attrs';

    /**
     * Tag content context.
     */
    public const CTX_TAG_CONTENT = 'tag_content';

    /**
     * Tag name context.
     */
    public const CTX_TAG_NAME = 'tag_name';

    /**
     * Ignore content of these tags.
     *
     * @var string[]
     */
    private static $ignoreTags = [
        'script'
    ];

    /**
     * Attrs with text contents.
     *
     * @var string[]
     */
    private static $textAttrs = [
        'abbr',
        'alt',
        'content',
        'label',
        'placeholder',
        'title'
    ];

    /**
     * Meta tag names with text content.
     *
     * @var string[]
     */
    private static $textMetaTags = [
        'description',
        'keywords'
    ];

    /**
     * Filter string.
     *
     * @param string $string String to be filtered.
     *
     * @return string Filtered string.
     *
     * @since 1.3
     */
    public function filter(string $string): string
    {
        $result = '';

        $string = $this->filterEntities($string);
        $string = $this->filterMetaTags($string);

        // Current/last tag name
        $tagName = null;
        // Current/last attribute name
        $attrName = null;
        // Current context
        $context = self::CTX_TAG_CONTENT;
        // Expected context
        $expecting = null;

        // By default tag content treated as text.
        $ignoreTagContent = false;
        // By default attribute values NOT treated as text.
        $ignoreAttrValue = true;

        $length = mb_strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($string, $i, 1);
            switch (true) {
                case '<' === $char:
                    $context = self::CTX_TAG_NAME;
                    $tagName = null;
                    $char = ' ';
                    break;

                case '>' === $char:
                    if ($this->isIgnoredTag($tagName)) {
                        $ignoreTagContent = true;
                    } elseif ('/' === $tagName[0]) {
                        $ignoreTagContent = false; // Restore to default state.
                    }
                    $context = self::CTX_TAG_CONTENT;
                    $expecting = null;
                    $char = ' ';
                    break;

                case ' ' === $char:
                case "\n" === $char:
                case "\t" === $char:
                    switch ($context) {
                        case self::CTX_TAG_NAME:
                            $context = self::CTX_TAG_ATTRS;
                            break;

                        case self::CTX_ATTR_NAME:
                            $context = self::CTX_TAG_ATTRS;
                            break;
                    }
                    break;

                case '=' === $char
                    && (self::CTX_ATTR_NAME === $context || self::CTX_TAG_ATTRS === $context):
                    $expecting = self::CTX_ATTR_VALUE;
                    $char = ' ';
                    break;

                case '"' === $char:
                case "'" === $char:
                    switch (true) {
                        case self::CTX_ATTR_VALUE === $expecting:
                            $context = self::CTX_ATTR_VALUE;
                            $ignoreAttrValue
                                = !in_array(strtolower($attrName), self::$textAttrs, true);
                            $expecting = null;
                            $char = ' ';
                            break;

                        case self::CTX_ATTR_VALUE === $context:
                            $context = self::CTX_TAG_ATTRS;
                            $char = ' ';
                            break;
                    }
                    break;

                default:
                    switch ($context) {
                        case self::CTX_TAG_NAME:
                            $tagName .= $char;
                            $char = ' ';
                            break;

                        /** @noinspection PhpMissingBreakStatementInspection */
                        case self::CTX_TAG_ATTRS:
                            $context = self::CTX_ATTR_NAME;
                            $attrName = null;
                        // no break needed
                        case self::CTX_ATTR_NAME:
                            $attrName .= $char;
                            $char = ' ';
                            break;

                        case self::CTX_ATTR_VALUE:
                            if ($ignoreAttrValue) {
                                $char = ' ';
                            }
                            break;

                        case self::CTX_TAG_CONTENT:
                            if ($ignoreTagContent) {
                                $char = ' ';
                            }
                            break;
                    }
            }
            $result .= $char;
        }

        return $result;
    }

    /**
     * Replace HTML entities.
     *
     * @param string $string
     *
     * @return string
     */
    private function filterEntities(string $string): string
    {
        return preg_replace_callback(
            '/&\w+;/',
            function ($match) {
                return str_repeat(' ', strlen($match[0]));
            },
            $string
        );
    }

    /**
     * Replace non-text meta tags.
     *
     * @param string $string
     *
     * @return string
     */
    private function filterMetaTags(string $string): string
    {
        return preg_replace_callback(
            '/<meta[^>]+(http-equiv\s*=|name\s*=\s*["\']?([^>"\']+))[^>]*>/i',
            function ($match) {
                if (count($match) < 3
                    || !in_array(strtolower($match[2]), self::$textMetaTags, true)
                ) {
                    return str_repeat(' ', strlen($match[0]));
                }

                return $match[0];
            },
            $string
        );
    }

    /**
     * Return true if $name is in the list of ignored tags.
     *
     * @param null|string $name Tag name.
     *
     * @return bool
     */
    private function isIgnoredTag(?string $name): bool
    {
        foreach (self::$ignoreTags as $tag) {
            if (strcasecmp($tag, $name) === 0) {
                return true;
            }
        }

        return false;
    }
}
