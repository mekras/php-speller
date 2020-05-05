<?php

/**
 * PHP Speller
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mekras\Speller\Tests\Unit\Source;

use Mekras\Speller\Source\HtmlSource;
use Mekras\Speller\Source\StringSource;
use Mekras\Speller\Exception\SourceException;
use PHPUnit\Framework\TestCase;

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
    public function testBasics(): void
    {
        $source = new HtmlSource('<a href="#" title="Foo">Bar</a> Baz');
        static::assertEquals('                   Foo  Bar     Baz', $source->getAsString());
    }

    /**
     * Encoding should be detected from meta tags.
     */
    public function testEncoding(): void
    {
        $source = new HtmlSource(
            '<html><meta http-equiv="Content-Type" content="text/html; charset=koi8-r"></html>'
        );
        static::assertEquals('koi8-r', $source->getEncoding());
    }

    /**
     * HtmlSource should throw SourceException on invalid HTML.
     */
    public function testInvalidHtml(): void
    {
        $this->expectException(SourceException::class);
        $this->expectExceptionMessage('Opening and ending tag mismatch: a and b at 1:11');

        new HtmlSource(new StringSource('<a><b></a></b>'));
    }
}
