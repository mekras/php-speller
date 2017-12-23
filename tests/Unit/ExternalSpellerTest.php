<?php
/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Tests\Unit;

use Mekras\Speller\Exception\EnvironmentException;
use Mekras\Speller\Exception\ExternalProgramFailedException;
use Mekras\Speller\ExternalSpeller;
use Mekras\Speller\Source\EncodingAwareSource;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Tests for Mekras\Speller\ExternalSpeller.
 *
 * @covers \Mekras\Speller\ExternalSpeller
 */
class ExternalSpellerTest extends TestCase
{
    /**
     * Should raise an ExternalProgramFailedException
     */
    public function testProcessRunException()
    {
        $commandLine = 'aspell';
        $process = $this->prophesize(Process::class);

        $process->setCommandLine($commandLine)->shouldBeCalled();
        $process->inheritEnvironmentVariables(true)->shouldBeCalled();
        $process->setTimeout(600)->shouldBeCalled();
        $process->setEnv([])->shouldBeCalled();
        $process->setInput('')->shouldBeCalled();
        $process->getCommandLine()->shouldBeCalled()->willReturn($commandLine);

        $process->run()->willThrow(RuntimeException::class);

        $source = $this->prophesize(EncodingAwareSource::class);
        $source->getAsString()->shouldBeCalled()->willReturn('');

        $mock = $this->getMockForAbstractClass(ExternalSpeller::class, ['aspell']);
        $mock->setProcess($process->reveal());

        static::expectException(ExternalProgramFailedException::class);
        static::expectExceptionCode(0);
        static::expectExceptionMessage('Failed to execute "aspell":');

        $mock->checkText($source->reveal(), ['en']);
    }

    /**
     * Should raise an EnvironmentException if getExitCode failed on process
     */
    public function testProcessGetExitCode()
    {
        $commandLine = 'aspell';
        $process = $this->prophesize(Process::class);

        $process->setCommandLine($commandLine)->shouldBeCalled();
        $process->inheritEnvironmentVariables(true)->shouldBeCalled();
        $process->setTimeout(600)->shouldBeCalled();
        $process->setEnv([])->shouldBeCalled();
        $process->setInput('')->shouldBeCalled();
        $process->run()->shouldBeCalled();

        $exception = new EnvironmentException('Test exception', 111);
        $process->getExitCode()->willThrow($exception);

        $source = $this->prophesize(EncodingAwareSource::class);
        $source->getAsString()->shouldBeCalled()->willReturn('');

        $mock = $this->getMockForAbstractClass(ExternalSpeller::class, ['aspell']);
        $mock->setProcess($process->reveal());

        static::expectException(EnvironmentException::class);
        static::expectExceptionCode(111);
        static::expectExceptionMessage('Test exception');

        $mock->checkText($source->reveal(), ['en']);
    }

    /**
     * Should raise ExternalProgamFailedException on exitCode other than 0
     */
    public function testProcessExitCodeDifferFromZero()
    {
        $commandLine = 'aspell';
        $process = $this->prophesize(Process::class);

        $process->setCommandLine($commandLine)->shouldBeCalled();
        $process->inheritEnvironmentVariables(true)->shouldBeCalled();
        $process->setTimeout(600)->shouldBeCalled();
        $process->setEnv([])->shouldBeCalled();
        $process->setInput('')->shouldBeCalled();
        $process->run()->shouldBeCalled();
        $process->getCommandLine()->shouldBeCalled()->willReturn($commandLine);
        $process->getErrorOutput()->shouldBeCalled()->willReturn('Error');
        $process->getExitCode()->shouldBeCalled()->willReturn(1);

        $source = $this->prophesize(EncodingAwareSource::class);
        $source->getAsString()->shouldBeCalled()->willReturn('');

        $mock = $this->getMockForAbstractClass(ExternalSpeller::class, ['aspell']);
        $mock->setProcess($process->reveal());

        static::expectException(ExternalProgramFailedException::class);
        static::expectExceptionCode(1);
        static::expectExceptionMessage('Failed to execute "aspell": Error');

        $mock->checkText($source->reveal(), ['en']);
    }
}
