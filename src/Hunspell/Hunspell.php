<?php

/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mekras\Speller\Hunspell;

use Mekras\Speller\Exception\ExternalProgramFailedException;
use Mekras\Speller\Ispell\Ispell;
use Mekras\Speller\Source\EncodingAwareSource;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Hunspell adapter.
 *
 * @since 1.6 Derived from {@see Ispell}.
 * @since 1.0
 */
class Hunspell extends Ispell
{
    /**
     * Custom dictionary path.
     *
     * @var string|null
     */
    private $customDictPath;

    /**
     * Custom dictionaries list.
     *
     * @var string[]
     */
    private $customDictionaries = [];

    /**
     * Cache for list of supported languages
     *
     * @var string[]|null
     */
    private $supportedLanguages;

    /**
     * Create new hunspell adapter.
     *
     * @param string $binaryPath Path to hunspell binary (default "hunspell").
     *
     * @since 1.0
     */
    public function __construct(string $binaryPath = 'hunspell')
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
     * @since 1.0
     */
    public function getSupportedLanguages(): array
    {
        if (null === $this->supportedLanguages) {
            $process = $this->createProcess(['-D']);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ExternalProgramFailedException(
                    $process->getCommandLine(),
                    $process->getErrorOutput(),
                    $process->getExitCode()
                );
            }
            $this->resetProcess();

            $languages = [];

            $output = explode(PHP_EOL, $process->getErrorOutput());
            $is_win = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            foreach ($output as $line) {
                $line = trim($line);
                if (
                    '' === $line // Skip empty lines
                    || substr($line, -1) === ':' // Skip headers
                    || strpos($line, $is_win ? ';' : ':') !== false // Skip search path
                ) {
                    continue;
                }
                $name = basename($line);
                if (strpos($name, 'hyph_') === 0) {
                    // Skip MySpell hyphen files
                    continue;
                }
                $name = preg_replace('/\.(aff|dic)$/', '', $name);
                $languages[$name] = true;
            }

            $languages = array_keys($languages);
            sort($languages);
            $this->supportedLanguages = $languages;
        }

        return $this->supportedLanguages;
    }

    /**
     * Set additional dictionaries path.
     *
     * Set path using DICPATH environment variable. See hunspell(1) man page for details.
     *
     * @param string $path Path to dictionaries folder (e. g. "/some/path")
     *
     * @link  setCustomDictionaries()
     * @since 1.0
     */
    public function setDictionaryPath(string $path): void
    {
        $this->customDictPath = $path;
        $this->supportedLanguages = null; // Clear cache because $path can contain new languages
    }

    /**
     * Set list of additional dictionaries.
     *
     * @param string[] $customDictionaries List of file names without extensions.
     *
     * @link  setDictionaryPath()
     * @since 1.0
     */
    public function setCustomDictionaries(array $customDictionaries): void
    {
        $this->customDictionaries = $customDictionaries;
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
            '-i',
            ($source instanceof EncodingAwareSource ? $source->getEncoding() : 'UTF-8'),
            '-a' // Machine readable output
        ];

        if (count($languages)) {
            $dictionaries = $this->getLanguageMapper()
                ->map($languages, $this->getSupportedLanguages());
            $dictionaries = array_merge($dictionaries, $this->customDictionaries);
            if (count($dictionaries)) {
                $args[] = '-d';
                $args[] = implode(',', $dictionaries);
            }
        }

        return $args;
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
    protected function createEnvVars(EncodingAwareSource $source, array $languages): array
    {
        $vars = [];
        if ($this->customDictPath) {
            $vars['DICPATH'] = $this->customDictPath;
        }

        return $vars;
    }
}
