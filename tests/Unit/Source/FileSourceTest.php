<?php
/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Unit\Source;

use Mekras\Speller\Source\FileSource;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Mekras\Speller\Source\FileSource.
 *
 * @covers \Mekras\Speller\Source\FileSource
 */
class FileSourceTest extends TestCase
{
    /**
     * Test basics.
     */
    public function testBasics()
    {
        $filename = __DIR__ . '/fixtures/test.txt';
        $source = new FileSource($filename);
        static::assertEquals('Tiger, tiger, burning bright', $source->getAsString());
        static::assertEquals($filename, $source->getFilename());
    }

    /**
     * Test encoding.
     */
    public function testEncoding()
    {
        $source = new FileSource(__DIR__ . '/fixtures/test.txt');
        static::assertEquals('UTF-8', $source->getEncoding());

        $source = new FileSource(__DIR__ . '/fixtures/test.txt', 'koi8-r');
        static::assertEquals('koi8-r', $source->getEncoding());
    }

    /**
     * getAsString should throw SourceException if file not exists.
     *
     * @expectedException \Mekras\Speller\Exception\SourceException
     */
    public function testFileNotExists()
    {
        $source = new FileSource('non-existent.file');
        $source->getAsString();
    }
}
