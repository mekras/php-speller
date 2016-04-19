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
 * Filter replaces HTML tags with spaces
 *
 * @since 1.3
 */
class HtmlFilter implements Filter
{
    /**
     * Attrs with text contents
     *
     * @var string[]
     */
    private $textAttrs = ['title'];

    /**
     * Filter string
     *
     * @param string $string string to be filtered
     *
     * @return string filtered string
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
            $ch = mb_substr($string, $i, 1);
            switch ($ch) {

                case '<':
                    $context = 'tag_name';
                    $tagName = null;
                    $ch = ' ';
                    break;

                case '>':
                    $context = null;
                    $expecting = null;
                    $ch = ' ';
                    break;

                case ' ':
                case "\n":
                case "\t":
                    switch ($context) {

                        case 'tag_name':
                            $context = 'tag_attrs';
                            break;

                        case 'attr_name':
                            $context = 'tag_attrs';
                            break;
                    }
                    break;

                case '=':
                    if ('attr_name' === $context || 'tag_attrs' === $context) {
                        $expecting = 'attr_value';
                        $ch = ' ';
                    }
                    break;

                case '"':
                case "'":
                    switch (true) {

                        case 'attr_value' === $expecting:
                            $context = 'attr_value';
                            if (in_array(strtolower($attrName), $this->textAttrs, true)) {
                                $context = 'attr_text';
                            }
                            $expecting = null;
                            $ch = ' ';
                            break;

                        case 'attr_value' === $context:
                        case 'attr_text' === $context:
                            $context = 'tag_attrs';
                            $ch = ' ';
                            break;
                    }
                    break;

                default:
                    switch ($context) {

                        case 'tag_name':
                            $tagName .= $ch;
                            $ch = ' ';
                            break;

                        /** @noinspection PhpMissingBreakStatementInspection */
                        case 'tag_attrs':
                            $context = 'attr_name';
                            $attrName = null;
                        // no break needed
                        case 'attr_name':
                            $attrName .= $ch;
                            $ch = ' ';
                            break;

                        case 'attr_value':
                            $ch = ' ';
                            break;
                    }
            }
            $result .= $ch;
        }

        return $result;
    }

    /**
     * Replace HTML entities
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
