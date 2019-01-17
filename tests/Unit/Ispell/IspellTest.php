<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Unit\Ispell;

use Mekras\Speller\Ispell\Ispell;
use Mekras\Speller\Source\StringSource;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Mekras\Speller\Ispell\Ispell.
 *
 * @covers \Mekras\Speller\ExternalSpeller
 * @covers \Mekras\Speller\Ispell\Ispell
 */
class IspellTest extends TestCase
{
    /**
     * Test retrieving list of supported languages.
     */
    public function testGetSupportedLanguages(): void
    {
        $ispell = new Ispell(__DIR__ . '/fixtures/bin/ispell.sh');
        static::assertEquals(['english', 'russian'], $ispell->getSupportedLanguages());
    }

    /**
     * Test spell checking.
     *
     * See fixtures/input.txt for the source text.
     */
    public function testCheckText(): void
    {
        $ispell = new Ispell(__DIR__ . '/fixtures/bin/ispell.sh');
        $source = new StringSource('<will be ignored and loaded from fixtures/check.txt>');
        $issues = $ispell->checkText($source, ['en']);
        static::assertCount(5, $issues);
        static::assertEquals('Tigr', $issues[0]->word);
        static::assertEquals(1, $issues[0]->line);
        static::assertEquals(0, $issues[0]->offset);
        static::assertEquals(['Tier', 'Tiger'], $issues[0]->suggestions);

        static::assertEquals('theforests', $issues[1]->word);
        static::assertEquals(2, $issues[1]->line);
        static::assertEquals(3, $issues[1]->offset);
        static::assertCount(2, $issues[1]->suggestions);

        static::assertEquals('CCould', $issues[4]->word);
        static::assertEquals(4, $issues[4]->line);
    }
}
