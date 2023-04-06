<?php

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mekras\Speller\Tests\Functional;

use PHPUnit\Framework\TestCase;

/**
 * Check if aspell is installed otherwise skip tests
 *
 * @package Mekras\Speller\Tests\Functional
 * @author  icanhazstring <blubb0r05+github@gmail.com>
 */
class AspellTestCase extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $userBinary = '/usr/bin/aspell';
        $libBinary = '/usr/lib/aspell';
        $sharedBinary = '/usr/share/aspell';

        if (!(file_exists($userBinary) || file_exists($libBinary) || file_exists($sharedBinary))) {
            self::markTestSkipped('skipping tests - aspell binary not installed');
        }
    }
}
