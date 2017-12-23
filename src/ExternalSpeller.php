<?php
/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller;

use Mekras\Speller\Exception\EnvironmentException;
use Mekras\Speller\Exception\ExternalProgramFailedException;
use Mekras\Speller\Source\EncodingAwareSource;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Base class for external program adapters.
 *
 * @since 1.6
 */
abstract class ExternalSpeller implements Speller
{
    /**
     * Command to run external speller.
     *
     * @var string
     */
    private $binary;

    /**
     * Execution timeout in seconds.
     *
     * @var int
     */
    private $timeout = 600;

    /**
     * Internal process handling
     *
     * @var Process
     */
    private $process;

    /**
     * Create new adapter.
     *
     * @param string $binaryPath Command to run external speller.
     *
     * @since 1.6
     */
    public function __construct($binaryPath)
    {
        $this->binary = (string)$binaryPath;
    }

    /**
     * Check text.
     *
     * Check given text and return an array of spelling issues.
     *
     * @param EncodingAwareSource $source    Text source to check.
     * @param array               $languages List of languages used in text (IETF language tag).
     *
     * @return Issue[]
     *
     * @see   http://tools.ietf.org/html/bcp47
     * @since 1.6
     */
    public function checkText(EncodingAwareSource $source, array $languages)
    {
        $process = $this->createProcess($this->createArguments($source, $languages));
        $process->setEnv($this->createEnvVars($source, $languages));

        $process->setInput($source->getAsString());

        try {
            $process->run();
        } catch (RuntimeException $e) {
            throw new ExternalProgramFailedException(
                $process->getCommandLine(),
                $e->getMessage(),
                0,
                $e
            );
        }

        try {
            $exitCode = $process->getExitCode();
        } catch (RuntimeException $e) {
            throw new EnvironmentException($e->getMessage(), $e->getCode(), $e);
        }

        if (0 !== $exitCode) {
            throw new ExternalProgramFailedException(
                $process->getCommandLine(),
                $process->getErrorOutput() ?: $process->getOutput(),
                $exitCode
            );
        }

        $output = $process->getOutput();

        if ($source instanceof EncodingAwareSource
            && strcasecmp($source->getEncoding(), 'UTF-8') !== 0
        ) {
            $output = iconv($source->getEncoding(), 'UTF-8', $output);
        }

        return $this->parseOutput($output);
    }

    /**
     * Set external program execution timeout.
     *
     * @param int|float|null $seconds Timeout in seconds.
     *
     * @see   \Symfony\Component\Process\Process::setTimeout()
     * @since 1.6
     */
    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }


    /**
     * Compose shell command line
     *
     * @param string|string[]|null $args Ispell arguments.
     *
     * @return string
     *
     * @since 1.6
     */
    protected function composeCommand($args)
    {
        $command = $this->getBinary();
        if (is_array($args)) {
            $args = implode(' ', $args);
        }
        $command .= ' ' . $args;

        return $command;
    }

    /**
     * Return binary path.
     *
     * @return string
     *
     * @since 1.6
     */
    protected function getBinary()
    {
        return $this->binary;
    }

    /**
     * Create arguments for external speller.
     *
     * @param EncodingAwareSource $source    Text source to check.
     * @param array               $languages List of languages used in text (IETF language tag).
     *
     * @return string[]
     *
     * @since 1.6
     *
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    protected function createArguments(EncodingAwareSource $source, array $languages)
    {
        return [];
    }

    /**
     * Create environment variables for external speller.
     *
     * @param EncodingAwareSource $source    Text source to check.
     * @param array               $languages List of languages used in text (IETF language tag).
     *
     * @return string[]
     *
     * @since 1.6
     *
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    protected function createEnvVars(EncodingAwareSource $source, array $languages)
    {
        return [];
    }

    /**
     * Parse external speller output.
     *
     * @param string $output
     *
     * @return Issue[]
     *
     * @since 1.6
     */
    abstract protected function parseOutput($output);

    /**
     * Create new instance of external program.
     *
     * @param string|string[]|null $args Command arguments.
     *
     * @return Process
     *
     * @throws ExternalProgramFailedException
     * @throws InvalidArgumentException
     *
     * @since 1.6
     */
    protected function createProcess($args = null)
    {
        $command = $this->composeCommand($args);

        try {
            $process = $this->composeProcess($command);
        } catch (RuntimeException $e) {
            throw new ExternalProgramFailedException($command, $e->getMessage(), 0, $e);
        }

        return $process;
    }

    /**
     * Compose a process with given command. If no process is given in current instance a new one will be created.
     *
     * @param string $command
     *
     * @return Process
     *
     * @since 2.0
     */
    private function composeProcess(string $command)
    {
        if ($this->process === null) {
            $this->process = new Process($command);
        }

        $this->process->inheritEnvironmentVariables(true);
        $this->process->setTimeout($this->timeout);
        $this->process->setCommandLine($command);

        return $this->process;
    }

    /**
     * @param Process $process
     *
     * @return self
     */
    public function setProcess(Process $process)
    {
        $this->process = $process;

        return $this;
    }
}
