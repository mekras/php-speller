<?php
declare(strict_types=1);

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
    public function testProcessRunException(): void
    {
        $commandLine = 'aspell';
        $process = $this->prophesize(Process::class);

        $process->setCommandLine($commandLine)->shouldBeCalled();
        $process->inheritEnvironmentVariables()->shouldBeCalled();
        $process->setTimeout(600)->shouldBeCalled();
        $process->setEnv([])->shouldBeCalled();
        $process->setInput('')->shouldBeCalled();
        $process->getCommandLine()->shouldBeCalled()->willReturn($commandLine);

        $process->run()->willThrow(RuntimeException::class);

        $source = $this->prophesize(EncodingAwareSource::class);
        $source->getAsString()->shouldBeCalled()->willReturn('');

        $mock = $this->getMockForAbstractClass(ExternalSpeller::class, ['aspell']);
        $mock->setProcess($process->reveal());

        $this->expectException(ExternalProgramFailedException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Failed to execute "aspell":');

        $mock->checkText($source->reveal(), ['en']);
    }

    /**
     * Should raise an EnvironmentException if getExitCode failed on process
     */
    public function testProcessGetExitCode(): void
    {
        $commandLine = 'aspell';
        $process = $this->prophesize(Process::class);

        $process->setCommandLine($commandLine)->shouldBeCalled();
        $process->inheritEnvironmentVariables()->shouldBeCalled();
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

        $this->expectException(EnvironmentException::class);
        $this->expectExceptionCode(111);
        $this->expectExceptionMessage('Test exception');

        $mock->checkText($source->reveal(), ['en']);
    }

    /**
     * Should raise ExternalProgamFailedException on exitCode other than 0
     */
    public function testProcessExitCodeDifferFromZero(): void
    {
        $commandLine = 'aspell';
        $process = $this->prophesize(Process::class);

        $process->setCommandLine($commandLine)->shouldBeCalled();
        $process->inheritEnvironmentVariables()->shouldBeCalled();
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

        $this->expectException(ExternalProgramFailedException::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('Failed to execute "aspell": Error');

        $mock->checkText($source->reveal(), ['en']);
    }
}
