<?php

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mekras\Speller\Tests\Unit\Aspell;

use Mekras\Speller\Aspell\Aspell;
use Mekras\Speller\Source\EncodingAwareSource;
use Mekras\Speller\Source\StringSource;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * Tests for Mekras\Speller\Aspell\Aspell.
 *
 * @covers \Mekras\Speller\ExternalSpeller
 * @covers \Mekras\Speller\Aspell\Aspell
 */
class AspellTest extends TestCase
{
    protected static $input;
    protected static $dicts;
    protected static $check;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$input = file_get_contents(__DIR__ . '/fixtures/input.txt');
        self::$dicts = file_get_contents(__DIR__ . '/fixtures/dicts.txt');
        self::$check = file_get_contents(__DIR__ . '/fixtures/check.txt');
    }

    /**
     * Test retrieving list of supported languages.
     */
    public function testGetSupportedLanguages(): void
    {
        $process = $this->prophesize(Process::class);

        $process->setTimeout(600)->shouldBeCalled();
        $process->run()->shouldBeCalled();
        $process->isSuccessful()->shouldBeCalled()->willReturn(true);
        $process->getOutput()->shouldBeCalled()->willReturn(self::$dicts);

        $aspell = new Aspell();
        $aspell->setProcess($process->reveal());

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
                'ru-yo',
            ],
            $aspell->getSupportedLanguages()
        );
    }

    /**
     * Test spell checking.
     *
     * See fixtures/input.txt for the source text.
     */
    public function testCheckText(): void
    {
        $process = $this->prophesize(Process::class);

        $process->setTimeout(600)->shouldBeCalled();
        $process->setEnv([])->shouldBeCalled();
        $process->setInput(self::$input)->shouldBeCalled();
        $process->run()->shouldBeCalled();
        $process->getExitCode()->shouldBeCalled()->willReturn(0);
        $process->getOutput()->shouldBeCalled()->willReturn(self::$check);

        $aspell = new Aspell();
        $aspell->setProcess($process->reveal());

        $source = $this->prophesize(EncodingAwareSource::class);
        // getAsString will be called, but ignored due to aspell binary stub
        $source->getEncoding()->shouldBeCalled()->willReturn('UTF-8');
        $source->getAsString()->shouldBeCalled()->willReturn(self::$input);

        $issues = $aspell->checkText($source->reveal(), ['en']);

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

    /**
     * Test spell checking when a word contains a colon
     *
     * @see https://github.com/mekras/php-speller/issues/24
     */
    public function testCheckTextWithColon(): void
    {
        $source = new StringSource('S:t Petersburg är i Ryssland', 'UTF-8');

        $process = $this->prophesize(Process::class);
        $process->setTimeout(600)->shouldBeCalled();
        $process->setEnv([])->shouldBeCalled();
        $process->setInput('S:t Petersburg är i Ryssland')->shouldBeCalled();
        $process->run()->shouldBeCalled();
        $process->getExitCode()->shouldBeCalled()->willReturn(0);
        $process->getOutput()->shouldBeCalled()->willReturn(file_get_contents(__DIR__ . '/fixtures/check_sv.txt'));

        $aspell = new Aspell();
        $aspell->setProcess($process->reveal());
        $issues = $aspell->checkText($source, ['sv']);

        static::assertCount(1, $issues);
        static::assertEquals('S:t', $issues[0]->word);
        static::assertEquals(1, $issues[0]->line);
        static::assertEquals(0, $issues[0]->offset);
        static::assertEquals(['St', 'Set', 'Sot', 'Söt', 'Stl', 'Stå'], $issues[0]->suggestions);
    }

    /**
     * Test spell checking when aspell binary output is unexpected
     */
    public function testUnexpectedOutputParsing(): void
    {
        $source = new StringSource('The quick brown fox jumps over the lazy dog', 'UTF-8');

        $process = $this->prophesize(Process::class);
        $process->setTimeout(600)->shouldBeCalled();
        $process->setEnv([])->shouldBeCalled();
        $process->setInput('The quick brown fox jumps over the lazy dog')->shouldBeCalled();
        $process->run()->shouldBeCalled();
        $process->getExitCode()->shouldBeCalled()->willReturn(0);
        $process->getOutput()->shouldBeCalled()->willReturn('& unexpected output: foo, bar, baz');

        $aspell = new Aspell();
        $aspell->setProcess($process->reveal());
        $issues = $aspell->checkText($source, ['en']);

        static::assertEmpty($issues);
    }
}
