<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

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
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $userBinary = '/usr/bin/aspell';
        $libBinary = '/usr/lib/aspell';
        $sharedBinary = '/usr/share/aspell';

        if (!(file_exists($userBinary) || file_exists($libBinary) || file_exists($sharedBinary))) {
            $this->markTestSkipped('skipping tests - aspell binary not installed');
        }
    }
}
