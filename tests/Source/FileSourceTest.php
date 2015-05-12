<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Tests\Source;

use Mekras\Speller\Source\FileSource;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Tests for Mekras\Speller\Source\FileSource
 *
 * @covers Mekras\Speller\Source\FileSource
 */
class FileSourceTest extends TestCase
{
    /**
     * Test basic functions
     */
    public function testBasics()
    {
        $source = new FileSource(__DIR__ . '/fixtures/test.txt');
        static::assertEquals('Tiger, tiger, burning bright', $source->getAsString());
    }
}
