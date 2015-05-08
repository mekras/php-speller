<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Tests\Source;

use Mekras\Speller\Source\StringSource;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Tests for Mekras\Speller\Source\StringSource
 *
 * @covers Mekras\Speller\Source\StringSource
 */
class StringSourceTest extends TestCase
{
    /**
     * Test basic functions
     */
    public function testBasics()
    {
        $source = new StringSource('foo bar');
        static::assertEquals('foo bar', $source->getAsString());
    }
}
