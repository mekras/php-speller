<?php
declare(strict_types=1);

/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Tests\Unit;

use Mekras\Speller\Issue;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Mekras\Speller\Issue
 *
 * @covers \Mekras\Speller\Issue
 */
class IssueTest extends TestCase
{
    /**
     * Test basic functions
     */
    public function testBasics(): void
    {
        $issue = new Issue('foo');
        static::assertEquals('foo', $issue->word);
        static::assertEquals(Issue::UNKNOWN_WORD, $issue->code);
        static::assertEquals([], $issue->suggestions);
        static::assertNull($issue->line);
        static::assertNull($issue->offset);
    }
}
