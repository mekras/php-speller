<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Hunspell;

use Mekras\Speller\Helper\LanguageMapper;
use Mekras\Speller\Issue;
use Mekras\Speller\Source\Source;
use Mekras\Speller\Speller;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Hunspell adapter
 *
 * @since 1.0
 */
class Hunspell implements Speller
{
    /**
     * Command to run hunspell
     *
     * @var string
     */
    private $binary;

    /**
     * Execution timeout in seconds
     *
     * @var int
     */
    private $timeout = 600;

    /**
     * Путь к собственным словарям
     *
     * @var string|null
     */
    private $customDictPath = null;

    /**
     * Список дополнительных словарей
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
     * Language mapper
     *
     * @var LanguageMapper|null
     */
    private $languageMapper = null;

    /**
     * Create new hunspell adapter
     *
     * @param string $hunspellBinary command to run hunspell (default "hunspell")
     *
     * @since 1.0
     */
    public function __construct($hunspellBinary = 'hunspell')
    {
        $this->binary = (string) $hunspellBinary;
    }

    /**
     * Check text
     *
     * Check given text and return an array of spelling issues.
     *
     * @param Source $source    text source to check
     * @param array  $languages list of languages used in text (IETF language tag)
     *
     * @throws \RuntimeException if hunspell returns non zero exit code
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws RuntimeException
     *
     * @return Issue[]
     *
     * @link  http://tools.ietf.org/html/bcp47
     * @since 1.0
     */
    public function checkText(Source $source, array $languages)
    {
        $dictionaries = $this->getLanguageMapper()->map($languages, $this->getSupportedLanguages());
        $dictionaries = array_merge($dictionaries, $this->customDictionaries);

        $process = $this->createProcess(
            [
                '-i UTF-8', // Input encoding
                '-a', // Machine readable output
                '-d ' . implode(',', $dictionaries)
            ]
        );

        $process->setInput($source->getAsString());
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('hunspell: %s', $process->getErrorOutput()));
        }
        $result = $process->getOutput();
        $result = explode(PHP_EOL, $result);
        $issues = [];
        $lineNo = 1;
        foreach ($result as $line) {
            $line = trim($line);
            if ('' === $line) {
                // Go to the next line
                $lineNo++;
                continue;
            }
            $parts = explode(' ', $line);
            $code = array_shift($parts);
            if ('#' === $code || '&' === $code) {
                $word = array_shift($parts);
                $issue = new Issue($word);
                $issue->line = $lineNo;
                $issue->offset = trim(array_shift($parts));
                $issues [] = $issue;
                if ('&' === $code) {
                    $issue->offset = trim(array_shift($parts), ':');
                    $issue->suggestions = array_map(
                        function ($word) {
                            return trim($word, ', ');
                        },
                        $parts
                    );
                }
            }
        }

        return $issues;
    }

    /**
     * Return list of supported languages
     *
     * @return string[]
     *
     * @throws \RuntimeException if hunspell returns non zero exit code
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
                throw new \RuntimeException(sprintf('hunspell: %s', $process->getErrorOutput()));
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
     * Set additional dictionaries path
     *
     * Set path using DICPATH environment variable. See hunspell(1) man page for details.
     *
     * @param string $path path to dictionaries folder (e. g. "/some/path")
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
     * Set list of additional dictionaries
     *
     * @param string[] $customDictionaries list of file names without extensions
     *
     * @link  setDictionaryPath()
     * @since 1.0
     */
    public function setCustomDictionaries(array $customDictionaries)
    {
        $this->customDictionaries = $customDictionaries;
    }

    /**
     * Set language mapper
     *
     * @param LanguageMapper $mapper
     *
     * @since 1.1
     */
    public function setLanguageMapper(LanguageMapper $mapper)
    {
        $this->languageMapper = $mapper;
    }

    /**
     * Set hunspell execution timeout
     *
     * @param int|float|null $seconds timeout in seconds
     *
     * @see   Symfony\Component\Process\Process::setTimeout()
     * @since 1.0
     */
    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    /**
     * Create new instance of hunspell process
     *
     * @param string|string[]|null $args hunspell arguments
     * @param array                $env  environment variables
     *
     * @return Process
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    private function createProcess($args = null, array $env = [])
    {
        $command = $this->composeCommand($args, $env);
        $process = new Process($command);
        $process->setTimeout($this->timeout);

        return $process;
    }

    /**
     * Compose shell command line
     *
     * @param string|string[]|null $args hunspell arguments
     * @param array                $env  environment variables
     *
     * @return string
     */
    private function composeCommand($args, array $env = [])
    {
        $command = $this->binary;
        if ($this->customDictPath) {
            $env['DICPATH'] = $this->customDictPath;
        }
        if (count($env) > 0) {
            foreach ($env as $name => $value) {
                $command = $name . '=' . escapeshellarg($value) . ' ' . $command;
            }
        }
        if (is_array($args)) {
            $args = implode(' ', $args);
        }
        $command .= ' ' . $args;

        return $command;
    }

    /**
     * Return language mapper
     *
     * @return LanguageMapper
     */
    private function getLanguageMapper()
    {
        if (null === $this->languageMapper) {
            $this->languageMapper = new LanguageMapper();
        }

        return $this->languageMapper;
    }
}
