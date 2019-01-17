<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Functional\Aspell;

use Mekras\Speller\Aspell\Aspell;
use Mekras\Speller\Dictionary;
use Mekras\Speller\Issue;
use Mekras\Speller\Source\StringSource;
use Mekras\Speller\Tests\Functional\AspellTestCase;

/**
 * Functional test with aspell
 *
 * @package Mekras\Speller\Tests\Functional\Aspell
 * @author  icanhazstring <blubb0r05+github@gmail.com>
 */
class AspellTest extends AspellTestCase
{
    /**
     * Functional testing with aspell to check if personal dictionary is working
     */
    public function testPersonalDictionary(): void
    {
        // Take german word for testing purpose so we won't get any suggestion
        $source = new StringSource('Versicherungspolica');

        $aspell = new Aspell();
        $issues = $aspell->checkText($source, ['en']);

        $this->assertEmpty($issues[0]->suggestions);
        $this->assertEquals(Issue::UNKNOWN_WORD, $issues[0]->code);

        $aspell->setPersonalDictionary(new Dictionary(__DIR__ . '/fixtures/custom.en.pws'));
        $issues = $aspell->checkText($source, ['en']);

        $this->assertCount(1, $issues);
        $this->assertCount(1, $issues[0]->suggestions);
        $this->assertEquals(Issue::UNKNOWN_WORD, $issues[0]->code);
        $this->assertEquals('Versicherungspolice', $issues[0]->suggestions[0]);
    }
}
