<?php
/**
 * PHP Speller
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Source;

use Mekras\Speller\Source\HtmlSource;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Tests for Mekras\Speller\Source\HtmlSource.
 *
 * @covers \Mekras\Speller\Source\HtmlSource
 */
class HtmlSourceTest extends TestCase
{
    /**
     * Test basics.
     */
    public function testBasics()
    {
        $source = new HtmlSource('<a href="#" title="Foo">Bar</a> Baz');
        static::assertEquals('Foo  Bar Baz', $source->getAsString());
    }

    /**
     * Only for "keywords" and "description" meta tags "content" attr should be treated as string.
     */
    public function testMetaContent()
    {
        $source = new HtmlSource(
            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' .
            '<meta name="Keywords" content="Foo">' .
            '<meta name="description" content="Bar">'
        );
        static::assertEquals('Foo Bar', $source->getAsString());
    }
}
