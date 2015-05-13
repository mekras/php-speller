<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Tests\Source\Filter;

use Mekras\Speller\Source\Filter\HtmlFilter;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Tests for Mekras\Speller\Source\Filter\HtmlFilter
 *
 * @covers Mekras\Speller\Source\Filter\HtmlFilter
 */
class HtmlFilterTest extends TestCase
{
    /**
     * Test basic functional
     */
    public function testBasics()
    {
        $filter = new HtmlFilter();
        $html = "<br>foo <a\nhref = '#' title='bar'>\nbaz</a>";
        $text = "    foo   \n                  bar  \nbaz    ";
        static::assertEquals($text, $filter->filter($html));
    }
}
