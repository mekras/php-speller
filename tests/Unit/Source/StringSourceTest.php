<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Unit\Source;

use Mekras\Speller\Source\StringSource;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Mekras\Speller\Source\StringSource.
 *
 * @covers \Mekras\Speller\Source\StringSource
 */
class StringSourceTest extends TestCase
{
    /**
     * Test basics.
     */
    public function testBasics(): void
    {
        $source = new StringSource('foo bar');
        static::assertEquals('foo bar', $source->getAsString());
        static::assertEquals('UTF-8', $source->getEncoding());
    }

    /**
     * Test encoding.
     */
    public function testEncoding(): void
    {
        $source = new StringSource('foo');
        static::assertEquals('UTF-8', $source->getEncoding());

        $source = new StringSource('foo', 'koi8-r');
        static::assertEquals('koi8-r', $source->getEncoding());
    }
}
