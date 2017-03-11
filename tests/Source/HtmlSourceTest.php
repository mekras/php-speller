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
        static::assertEquals('                   Foo  Bar     Baz', $source->getAsString());
    }

    /**
     * Encoding should be detected from meta tags.
     */
    public function testEncoding()
    {
        $source = new HtmlSource(
            '<html><meta http-equiv="Content-Type" content="text/html; charset=koi8-r"></html>'
        );
        static::assertEquals('koi8-r', $source->getEncoding());
    }
}
