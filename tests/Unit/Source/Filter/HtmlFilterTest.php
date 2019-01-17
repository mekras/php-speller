<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Unit\Source\Filter;

use Mekras\Speller\Source\Filter\HtmlFilter;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Mekras\Speller\Source\Filter\HtmlFilter.
 *
 * @covers \Mekras\Speller\Source\Filter\HtmlFilter
 */
class HtmlFilterTest extends TestCase
{
    /**
     * Test basics.
     */
    public function testBasics(): void
    {
        $filter = new HtmlFilter();
        $html = "<br>foo&reg; <a\nhref = '#' title='bar'>\nbaz</a>";
        $text = "    foo        \n                  bar  \nbaz    ";
        static::assertEquals($text, $filter->filter($html));
    }

    /**
     * Only for "keywords" and "description" meta tags "content" attr should be treated as string.
     */
    public function testMetaContent(): void
    {
        $filter = new HtmlFilter();
        $html =
            '<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html" />' . "\n" .
            '<meta name="Keywords" content="Foo">' . "\n" .
            '<meta name="foo" content="Foobar">' . "\n" .
            '<meta name="description" content="Bar">';
        $text =
            "                                                      \n" .
            "                               Foo  \n" .
            "                                  \n" .
            '                                  Bar  ';
        static::assertEquals($text, $filter->filter($html));
    }

    /**
     * <script> content should be filtered out.
     */
    public function testScript(): void
    {
        $filter = new HtmlFilter();
        $html = "<p>Foo</p>\n<script type=\"text/javascript\">Bar Baz\nBuz</script>";
        $text = "   Foo    \n                                      \n            ";
        static::assertEquals($text, $filter->filter($html));
    }
}
