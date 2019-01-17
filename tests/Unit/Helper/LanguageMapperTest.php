<?php
declare(strict_types=1);

/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Tests\Unit\Helper;

use Mekras\Speller\Helper\LanguageMapper;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Mekras\Speller\Helper\LanguageMapper
 *
 * @covers \Mekras\Speller\Helper\LanguageMapper
 */
class LanguageMapperTest extends TestCase
{
    /**
     * Test basic mapping
     */
    public function testBasics(): void
    {
        $mapper = new LanguageMapper();
        $result = $mapper->map(
            ['de', 'en', 'ru'],
            ['de_DE', 'en_GB.UTF-8', 'en_US', 'ru_RU', 'ru']
        );
        static::assertEquals(['de_DE', 'en_GB.UTF-8', 'ru'], $result);
    }

    /**
     * Test preferred mapping
     */
    public function testPreferred(): void
    {
        $mapper = new LanguageMapper();
        $mapper->setPreferredMappings(['en' => ['en_US', 'en_GB']]);
        $result = $mapper->map(
            ['de', 'en', 'ru'],
            ['de_DE', 'en_GB.UTF-8', 'en_US', 'ru_RU', 'ru']
        );
        static::assertEquals(['de_DE', 'en_US', 'ru'], $result);
    }
}
