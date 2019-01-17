<?php
declare(strict_types=1);

/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Unit\Hunspell;

use Mekras\Speller\Hunspell\Hunspell;
use Mekras\Speller\Source\StringSource;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Tests for Mekras\Speller\Hunspell\Hunspell
 *
 * @covers \Mekras\Speller\ExternalSpeller
 * @covers \Mekras\Speller\Hunspell\Hunspell
 */
class HunspellTest extends TestCase
{
    /**
     * Test hunspell argument escaping
     * @throws \ReflectionException
     */
    public function testArgumentEscaping(): void
    {
        $hunspell = new Hunspell();
        $method = new ReflectionMethod(get_class($hunspell), 'composeCommand');
        $method->setAccessible(true);
        static::assertEquals('hunspell -d foo,bar', $method->invoke($hunspell, ['-d foo,bar']));
    }

    /**
     * Test retrieving list of supported languages
     */
    public function testGetSupportedLanguages(): void
    {
        $hunspell = new Hunspell($this->getBinary());
        static::assertEquals(
            ['de_BE', 'de_DE', 'de_LU', 'en-GB', 'en_AU', 'en_GB', 'en_US', 'en_ZA', 'ru_RU'],
            $hunspell->getSupportedLanguages()
        );
    }

    /**
     * Test spell checking
     *
     * See fixtures/input.txt for the source text.
     */
    public function testCheckText(): void
    {
        $hunspell = new Hunspell($this->getBinary());
        $source = new StringSource('<will be ignored and loaded from fixtures/check.txt>');
        $issues = $hunspell->checkText($source, ['en']);
        static::assertCount(6, $issues);
        static::assertEquals('Tigr', $issues[0]->word);
        static::assertEquals(1, $issues[0]->line);
        static::assertEquals(0, $issues[0]->offset);
        static::assertEquals(
            ['Ti gr', 'Ti-gr', 'Tiger', 'Trig', 'Tier', 'Tigris', 'Grit', 'Tigress', 'Tagore'],
            $issues[0]->suggestions
        );

        static::assertEquals('страх', $issues[1]->word);
        static::assertEquals(1, $issues[1]->line);
        static::assertEquals(21, $issues[1]->offset);
        static::assertCount(0, $issues[1]->suggestions);

        static::assertEquals('CCould', $issues[5]->word);
        static::assertEquals(4, $issues[5]->line);
    }

    /**
     * Return hunspell binary stub.
     *
     * @return string
     */
    private function getBinary(): string
    {
        return __DIR__ . '/fixtures/bin/hunspell.php';
    }
}
