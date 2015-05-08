<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Tests\Helper;

use Mekras\Speller\Helper\LanguageMapper;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Tests for Mekras\Speller\Helper\LanguageMapper
 *
 * @covers Mekras\Speller\Helper\LanguageMapper
 */
class LanguageMapperTest extends TestCase
{
    public function testBasics()
    {
        $result = LanguageMapper::map(
            ['de', 'en', 'ru'],
            ['de_DE', 'en_GB.UTF-8', 'en_US', 'ru_RU', 'ru']
        );
        static::assertEquals(['de_DE', 'en_GB.UTF-8', 'ru'], $result);
    }
}
