<?php
/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Source\Filter;

use Mekras\Speller\Source\Filter\HtmlFilter;
use PHPUnit_Framework_TestCase as TestCase;

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
    public function testBasics()
    {
        $filter = new HtmlFilter();
        $html = "<br>foo&reg; <a\nhref = '#' title='bar'>\nbaz</a>";
        $text = "    foo        \n                  bar  \nbaz    ";
        static::assertEquals($text, $filter->filter($html));
    }

    /**
     * Only for "keywords" and "description" meta tags "content" attr should be treated as string.
     */
    public function testMetaContent()
    {
        $filter = new HtmlFilter();
        $html =
            '<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html" />' . "\n" .
            '<meta name="Keywords" content="Foo">' . "\n" .
            '<meta name="description" content="Bar">';
        $text =
            "                                                      \n" .
            "                               Foo  \n" .
            '                                  Bar  ';
        static::assertEquals($text, $filter->filter($html));
    }
}
