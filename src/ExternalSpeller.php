<?php
/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Base class for external program adapters.
 *
 * @since x.x
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
     * Create new adapter.
     *
     * @param string $binaryPath Command to run external speller.
     *
     * @since x.x
     */
    public function __construct($binaryPath)
    {
        $this->binary = (string) $binaryPath;
    }

    /**
     * Set external program execution timeout.
     *
     * @param int|float|null $seconds Timeout in seconds.
     *
     * @see   \Symfony\Component\Process\Process::setTimeout()
     * @since x.x
     */
    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    /**
     * Compose shell command line.
     *
     * @param string|string[]|null $args Command arguments.
     * @param array                $env  Environment variables.
     *
     * @return string
     *
     * @since x.x
     */
    abstract protected function composeCommand($args, array $env = []);

    /**
     * Return binary path.
     *
     * @return string
     *
     * @since x.x
     */
    protected function getBinary()
    {
        return $this->binary;
    }

    /**
     * Create new instance of external program.
     *
     * @param string|string[]|null $args Command arguments.
     * @param array                $env  Environment variables
     *
     * @return Process
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function createProcess($args = null, array $env = [])
    {
        $command = $this->composeCommand($args, $env);
        $process = new Process($command);
        $process->setTimeout($this->timeout);

        return $process;
    }
}
