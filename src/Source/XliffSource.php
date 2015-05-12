<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Source;

use Mekras\Speller\Source\Filter\Filter;
use Mekras\Speller\Source\Filter\StripAllFilter;

/**
 * XLIFF translations as text source
 *
 * @link http://docs.oasis-open.org/xliff/xliff-core/v2.0/xliff-core-v2.0.html
 * @since x.xx
 */
class XliffSource extends FileSource
{
    /**
     * Text filters
     *
     * @var Filter[]|null[]
     */
    private $filters = [
        '#<!--.*?-->#ums' => null, // Comments
        '#<[^>]+>#ums' => null // Top level tags
    ];

    /**
     * Add custom pattern to be filtered
     *
     * Matched text will be filtered with a given filter or with
     * {@link Mekras\Speller\Source\Filter\StripAllFilter} if $filter is null.
     *
     * @param string $pattern PCRE pattern. It is recommended to use "ums" PCRE modifiers.
     * @param Filter $filter filter to be applied
     *
     * @since x.xx
     */
    public function addFilter($pattern, Filter $filter = null)
    {
        $this->filters[(string) $pattern] = $filter;
    }

    /**
     * Return text as one string
     *
     * @return string
     *
     * @since x.xx
     */
    public function getAsString()
    {
        $text = parent::getAsString();

        $stripAll = new StripAllFilter();

        /* Removing CDATA tags */
        $text = preg_replace_callback(
            '#<!\[CDATA\[(.*?)\]\]>#ums',
            function ($match) use ($stripAll) {
                $string = $match[1];
                $string = preg_replace_callback(
                    '#(<[^>]+>)([^<]*)(</[^>]+>)#',
                    function ($match) use ($stripAll) {
                        return $stripAll->filter($match[1]) . $match[2]
                        . $stripAll->filter($match[3]);
                    },
                    $string
                );
                return '         ' . $string . '  ';
            },
            $text
        );

        /* Processing bottom level tags */
        $text = preg_replace_callback(
            '#(<(\w+)(\s[^>]*?)?>)([^<>]*)(</\w+\s*>)#ums',
            function ($match) use ($stripAll) {
                if (strtolower($match[2]) == 'target') {
                    $replace = $stripAll->filter($match[1]) . $match[4]
                        . $stripAll->filter($match[5]);
                } else {
                    $replace = $stripAll->filter($match[0]);
                }
                return $replace;
            },
            $text
        );

        /* Other replacements */
        foreach ($this->filters as $pattern => $filter) {
            if (null === $filter) {
                $filter = $stripAll;
            }
            $text = preg_replace_callback(
                $pattern,
                function ($match) use ($filter) {
                    return $filter->filter($match[0]);
                },
                $text
            );
        }

        return $text;
    }
}
