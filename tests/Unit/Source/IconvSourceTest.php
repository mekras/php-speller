<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Unit\Source;

use Mekras\Speller\Source\IconvSource;
use Mekras\Speller\Source\StringSource;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Mekras\Speller\Source\IconvSource.
 *
 * @covers \Mekras\Speller\Source\IconvSource
 */
class IconvSourceTest extends TestCase
{
    /**
     * Test basics.
     */
    public function testBasics(): void
    {
        $source = new StringSource(iconv('utf-8', 'koi8-r', 'Привет'), 'koi8-r');
        $converter = new IconvSource($source);
        static::assertEquals('UTF-8', $converter->getEncoding());
        static::assertEquals('Привет', $converter->getAsString());
    }
}
