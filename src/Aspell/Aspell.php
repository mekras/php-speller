<?php

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mekras\Speller\Aspell;

use Mekras\Speller\Dictionary;
use Mekras\Speller\Exception\ExternalProgramFailedException;
use Mekras\Speller\Ispell\Ispell;
use Mekras\Speller\Source\EncodingAwareSource;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Aspell adapter.
 *
 * @since 1.6
 */
class Aspell extends Ispell
{
    /**
     * Cache for list of supported languages
     *
     * @var string[]|null
     */
    private $supportedLanguages;

    /**
     * @var Dictionary
     */
    private $personalDictionary;

    /**
     * Create new aspell adapter.
     *
     * @param string $binaryPath Path to aspell binary (default "aspell").
     *
     * @since 1.6
     */
    public function __construct(string $binaryPath = 'aspell')
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
     * @since 1.6
     */
    public function getSupportedLanguages(): array
    {
        if (null === $this->supportedLanguages) {
            $process = $this->createProcess(['dump', 'dicts']);
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
     * @param Dictionary $dictionary
     */
    public function setPersonalDictionary(Dictionary $dictionary): void
    {
        $this->personalDictionary = $dictionary;
        $this->resetProcess();
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
     */
    protected function createArguments(EncodingAwareSource $source, array $languages): array
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

        if ($this->personalDictionary !== null) {
            $args[] = '--personal=' . $this->personalDictionary->getPath();
        }

        return $args;
    }
}
