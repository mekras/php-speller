<?php
/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Aspell;

use Mekras\Speller\Exception\ExternalProgramFailedException;
use Mekras\Speller\Ispell\Ispell;
use Mekras\Speller\Source\EncodingAwareSource;
use Mekras\Speller\Source\Source;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Aspell adapter.
 *
 * @since x.x
 */
class Aspell extends Ispell
{
    /**
     * Cache for list of supported languages
     *
     * @var string[]|null
     */
    private $supportedLanguages = null;

    /**
     * Create new aspell adapter.
     *
     * @param string $binaryPath Path to aspell binary (default "aspell").
     *
     * @since x.x
     */
    public function __construct($binaryPath = 'aspell')
    {
        parent::__construct($binaryPath);
    }

    /**
     * Return list of supported languages.
     *
     * @return string[]
     *
     * @throws ExternalProgramFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws RuntimeException
     *
     * @since x.x
     */
    public function getSupportedLanguages()
    {
        if (null === $this->supportedLanguages) {
            $process = $this->createProcess('dump dicts');
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ExternalProgramFailedException(
                    $process->getCommandLine(),
                    $process->getErrorOutput(),
                    $process->getExitCode()
                );
            }

            $languages = [];

            $output = explode(PHP_EOL, $process->getOutput());
            foreach ($output as $line) {
                $name = trim($line);
                if (strpos($name, '-variant') !== false) {
                    // Skip variants
                    continue;
                }
                $languages[$name] = true;
            }

            $languages = array_keys($languages);
            sort($languages);
            $this->supportedLanguages = $languages;
        }

        return $this->supportedLanguages;
    }

    /**
     * Create arguments for external speller.
     *
     * @param Source $source    Text source to check.
     * @param array  $languages List of languages used in text (IETF language tag).
     *
     * @return string[]
     *
     * @throws ExternalProgramFailedException
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws RuntimeException
     *
     * @since x.x
     */
    protected function createArguments(Source $source, array $languages)
    {
        $args = [
            // Input encoding
            '--encoding=' . ($source instanceof EncodingAwareSource ? $source->getEncoding(
            ) : 'UTF-8'),
            '-a' // ispell compatible output
        ];

        if (count($languages) > 0) {
            $args[] = '--lang=' . $languages[0];
        }

        return $args;
    }
}
