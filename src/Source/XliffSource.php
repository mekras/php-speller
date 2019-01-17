<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source;

use Mekras\Speller\Source\Filter\Filter;
use Mekras\Speller\Source\Filter\HtmlFilter;
use Mekras\Speller\Source\Filter\StripAllFilter;

/**
 * XLIFF translations as text source.
 *
 * @since 1.6 derived from MetaSource.
 * @since 1.2
 *
 * @link  http://docs.oasis-open.org/xliff/xliff-core/v2.0/xliff-core-v2.0.html
 */
class XliffSource extends MetaSource
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
     * Create text source from XLIFF document.
     *
     * @param EncodingAwareSource|string $source Source of filename
     *
     * @throws \Mekras\Speller\Exception\SourceException
     *
     * @since 1.6 Accepts EncodingAwareSource
     *
     * @todo  deprecate string $source in version 2.0
     */
    public function __construct($source)
    {
        if (!$source instanceof EncodingAwareSource) {
            $source = new FileSource($source);
        }
        parent::__construct($source);
    }

    /**
     * Add custom pattern to be filtered.
     *
     * Matched text will be filtered with a given filter or with
     * {@link Mekras\Speller\Source\Filter\StripAllFilter} if $filter is null.
     *
     * @param string $pattern PCRE pattern. It is recommended to use "ums" PCRE modifiers.
     * @param Filter $filter  Filter to be applied.
     *
     * @since 1.2
     */
    public function addFilter(string $pattern, Filter $filter = null): void
    {
        $this->filters[$pattern] = $filter;
    }

    /**
     * Return text as one string
     *
     * @return string
     *
     * @throws \Mekras\Speller\Exception\SourceException
     * @since 1.2
     */
    public function getAsString(): string
    {
        $text = $this->source->getAsString();

        $stripAll = new StripAllFilter();
        $htmlFilter = new HtmlFilter();

        /* Removing CDATA tags */
        $text = preg_replace_callback(
            '#<!\[CDATA\[(.*?)\]\]>#ums',
            function ($match) use ($htmlFilter) {
                $string = $htmlFilter->filter($match[1]);

                //      <![CDATA[               ]]
                return '         ' . $string . '  ';
            },
            $text
        );

        /* Processing bottom level tags */
        $text = preg_replace_callback(
            '#(<(\w+)(\s[^>]*?)?>)([^<>]*)(</\w+\s*>)#um',
            function ($match) use ($stripAll) {
                if (strtolower($match[2]) === 'target') {
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
