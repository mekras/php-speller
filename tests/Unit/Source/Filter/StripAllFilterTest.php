<?php
declare(strict_types=1);

/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Tests\Unit\Source\Filter;

use Mekras\Speller\Source\Filter\StripAllFilter;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Mekras\Speller\Source\Filter\StripAllFilter
 *
 * @covers \Mekras\Speller\Source\Filter\StripAllFilter
 */
class StripAllFilterTest extends TestCase
{
    /**
     * Test basic functional
     */
    public function testBasics(): void
    {
        $filter = new StripAllFilter();
        static::assertEquals("   \n\t   ", $filter->filter("foo\n\tbar"));
    }
}
