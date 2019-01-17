<?php
declare(strict_types=1);

/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Unit\Source;

use Mekras\Speller\Source\XliffSource;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Mekras\Speller\Source\XliffSource.
 *
 * @covers \Mekras\Speller\Source\XliffSource
 */
class XliffSourceTest extends TestCase
{
    /**
     * Test basics.
     */
    public function testBasics(): void
    {
        $source = new XliffSource(__DIR__ . '/fixtures/test.xliff');
        static::assertEquals('UTF-8', $source->getEncoding());
        $source->addFilter('#\{\{[^}]+\}\}#ums');
        $lines = explode("\n", $source->getAsString());
        static::assertCount(36, $lines);
        static::assertEquals('Foo', substr($lines[7], 17, 3));
        static::assertEquals('Bar', substr($lines[12], 20, 3));
        static::assertEquals('Bar', substr($lines[13], 20, 3));
        static::assertNotContains('var', $lines[14]);
        static::assertEquals('Baz', substr($lines[27], 42, 3));
    }
}
