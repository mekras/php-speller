<?php
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
     * Attrs with text contents.
     *
     * @var string[]
     */
    static private $textAttrs = [
        'abbr',
        'alt',
        'content',
        'label',
        'placeholder',
        'title'
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
    public function filter($string)
    {
        $result = '';

        $string = $this->filterEntities($string);

        // Current/last tag name
        $tagName = null;
        // Current/last attribute name
        $attrName = null;
        // Current context
        $context = null;
        // Expected context
        $expecting = null;

        $length = mb_strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($string, $i, 1);
            switch (true) {
                case '<' === $char:
                    $context = 'tag_name';
                    $tagName = null;
                    $char = ' ';
                    break;

                case '>' === $char:
                    $context = null;
                    $expecting = null;
                    $char = ' ';
                    break;

                case ' ' === $char:
                case "\n" === $char:
                case "\t" === $char:
                    switch ($context) {
                        case 'tag_name':
                            $context = 'tag_attrs';
                            break;

                        case 'attr_name':
                            $context = 'tag_attrs';
                            break;
                    }
                    break;

                case '=' === $char && ('attr_name' === $context || 'tag_attrs' === $context):
                    $expecting = 'attr_value';
                    $char = ' ';
                    break;

                case '"' === $char:
                case "'" === $char:
                    switch (true) {
                        case 'attr_value' === $expecting:
                            $context = 'attr_value';
                            if (in_array(strtolower($attrName), self::$textAttrs, true)) {
                                $context = 'attr_text';
                            }
                            $expecting = null;
                            $char = ' ';
                            break;

                        case 'attr_value' === $context:
                        case 'attr_text' === $context:
                            $context = 'tag_attrs';
                            $char = ' ';
                            break;
                    }
                    break;

                default:
                    switch ($context) {
                        case 'tag_name':
                            $tagName .= $char;
                            $char = ' ';
                            break;

                        /** @noinspection PhpMissingBreakStatementInspection */
                        case 'tag_attrs':
                            $context = 'attr_name';
                            $attrName = null;
                        // no break needed
                        case 'attr_name':
                            $attrName .= $char;
                            $char = ' ';
                            break;

                        case 'attr_value':
                            $char = ' ';
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
    private function filterEntities($string)
    {
        return preg_replace_callback(
            '/&\w+;/',
            function ($match) {
                return str_repeat(' ', strlen($match[0]));
            },
            $string
        );
    }
}
