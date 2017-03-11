<?php
/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Hunspell;

use Mekras\Speller\Exception\ExternalProgramFailedException;
use Mekras\Speller\Ispell\Ispell;
use Mekras\Speller\Source\EncodingAwareSource;
use Mekras\Speller\Source\Source;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Hunspell adapter.
 *
 * @since x.x Derived from {@see Ispell}.
 * @since 1.0
 */
class Hunspell extends Ispell
{
    /**
     * Custom dictionary path.
     *
     * @var string|null
     */
    private $customDictPath = null;

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
    private $supportedLanguages = null;

    /**
     * Create new hunspell adapter.
     *
     * @param string $binaryPath Path to hunspell binary (default "hunspell").
     *
     * @since 1.0
     */
    public function __construct($binaryPath = 'hunspell')
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
     * @since 1.0
     */
    public function getSupportedLanguages()
    {
        if (null === $this->supportedLanguages) {
            $process = $this->createProcess('-D');
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ExternalProgramFailedException(
                    $process->getCommandLine(),
                    $process->getErrorOutput(),
                    $process->getExitCode()
                );
            }

            $languages = [];

            $output = explode(PHP_EOL, $process->getErrorOutput());
            foreach ($output as $line) {
                $line = trim($line);
                if ('' === $line // Skip empty lines
                    || substr($line, -1) === ':' // Skip headers
                    || strpos($line, ':') !== false // Skip search path
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
    public function setDictionaryPath($path)
    {
        $this->customDictPath = (string) $path;
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
    public function setCustomDictionaries(array $customDictionaries)
    {
        $this->customDictionaries = $customDictionaries;
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
            '-i ' . ($source instanceof EncodingAwareSource ? $source->getEncoding() : 'UTF-8'),
            '-a' // Machine readable output
        ];

        if (count($languages)) {
            $dictionaries = $this->getLanguageMapper()
                ->map($languages, $this->getSupportedLanguages());
            $dictionaries = array_merge($dictionaries, $this->customDictionaries);
            if (count($dictionaries)) {
                $args[] = '-d ' . implode(',', $dictionaries);
            }
        }

        return $args;
    }

    /**
     * Create environment variables for external speller.
     *
     * @param Source $source    Text source to check.
     * @param array  $languages List of languages used in text (IETF language tag).
     *
     * @return string[]
     *
     * @since x.x
     *
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    protected function createEnvVars(Source $source, array $languages)
    {
        $vars = [];
        if ($this->customDictPath) {
            $vars['DICPATH'] = $this->customDictPath;
        }

        return $vars;
    }
}
