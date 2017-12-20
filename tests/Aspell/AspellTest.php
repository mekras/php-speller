<?php
/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Aspell;

use Mekras\Speller\Aspell\Aspell;
use Mekras\Speller\Source\StringSource;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Mekras\Speller\Aspell\Aspell.
 *
 * @covers \Mekras\Speller\ExternalSpeller
 * @covers \Mekras\Speller\Aspell\Aspell
 */
class AspellTest extends TestCase
{
    /**
     * Test retrieving list of supported languages.
     */
    public function testGetSupportedLanguages()
    {
        $aspell = new Aspell(__DIR__ . '/fixtures/bin/aspell.sh');
        static::assertEquals(
            [
                'en',
                'en_GB',
                'en_GB-ise',
                'en_GB-ise-w_accents',
                'en_GB-ise-wo_accents',
                'en_GB-ize',
                'en_GB-ize-w_accents',
                'en_GB-ize-wo_accents',
                'ru',
                'ru-ye',
                'ru-yeyo',
                'ru-yo'
            ],
            $aspell->getSupportedLanguages()
        );
    }

    /**
     * Test spell checking.
     *
     * See fixtures/input.txt for the source text.
     */
    public function testCheckText()
    {
        $aspell = new Aspell(__DIR__ . '/fixtures/bin/aspell.sh');
        $source = new StringSource('<will be ignored and loaded from fixtures/check.txt>');
        $issues = $aspell->checkText($source, ['en']);
        static::assertCount(5, $issues);
        static::assertEquals('Tigr', $issues[0]->word);
        static::assertEquals(1, $issues[0]->line);
        static::assertEquals(0, $issues[0]->offset);
        static::assertEquals(['Ti gr', 'Ti-gr', 'Tiger', 'Tier'], $issues[0]->suggestions);

        static::assertEquals('theforests', $issues[1]->word);
        static::assertEquals(2, $issues[1]->line);
        static::assertEquals(3, $issues[1]->offset);
        static::assertCount(10, $issues[1]->suggestions);

        static::assertEquals('CCould', $issues[4]->word);
        static::assertEquals(4, $issues[4]->line);
    }
}
