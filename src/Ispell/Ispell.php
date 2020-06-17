<?php

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Mekras\Speller\Ispell;

use Mekras\Speller\Exception\EnvironmentException;
use Mekras\Speller\ExternalSpeller;
use Mekras\Speller\Helper\LanguageMapper;
use Mekras\Speller\Issue;
use Mekras\Speller\Source\EncodingAwareSource;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Ispell adapter.
 *
 * Notes:
 *
 * 1. since ispell allow to use only one dictionary at once, only first item taken from $languages
 *    argument in {@see checkText()}.
 *
 * @since 1.6
 */
class Ispell extends ExternalSpeller
{
    /**
     * Folder with bundled ispell dictionaries.
     *
     * @var string|null
     */
    private $bundledDictFolder;

    /**
     * Cache for list of supported languages
     *
     * @var string[]|null
     */
    private $supportedLanguages;

    /**
     * Language mapper
     *
     * @var LanguageMapper|null
     */
    private $languageMapper;

    /**
     * Create new ispell adapter.
     *
     * @param string      $binaryPath Command to run ispell (default "ispell").
     * @param string|null $dictFolder Folder with bundled ispell dictionaries (null — autodetect).
     *
     * @since 1.6
     */
    public function __construct(string $binaryPath = 'ispell', string $dictFolder = null)
    {
        parent::__construct($binaryPath);
        $this->bundledDictFolder = $dictFolder;
    }

    /**
     * Return list of supported languages.
     *
     * @return string[]
     *
     * @throws EnvironmentException
     * @throws LogicException
     * @throws RuntimeException
     * @since 1.6
     */
    public function getSupportedLanguages(): array
    {
        if (null === $this->supportedLanguages) {
            $this->supportedLanguages = [];
            $files = new \DirectoryIterator($this->getDictionaryFolder());
            foreach ($files as $file) {
                if ($file->getExtension() === 'aff') {
                    $this->supportedLanguages[] = $file->getBasename('.aff');
                }
            }
            sort($this->supportedLanguages);
        }

        return $this->supportedLanguages;
    }

    /**
     * Set language mapper.
     *
     * @param LanguageMapper $mapper
     *
     * @since 1.6
     */
    public function setLanguageMapper(LanguageMapper $mapper): void
    {
        $this->languageMapper = $mapper;
    }

    /**
     * Return language mapper.
     *
     * @return LanguageMapper
     *
     * @since 1.6
     */
    protected function getLanguageMapper(): LanguageMapper
    {
        if (null === $this->languageMapper) {
            $this->languageMapper = new LanguageMapper();
        }

        return $this->languageMapper;
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
    protected function createArguments(EncodingAwareSource $source, array $languages): array
    {
        $args = [
            '-a', // Machine readable output
        ];

        if (count($languages) > 0) {
            $language = $languages[0];
            $dictionaries = $this->getLanguageMapper()
                ->map([$language], $this->getSupportedLanguages());
            if (count($dictionaries) > 0) {
                $args[] = '-d ' . $dictionaries[0];
            }
        }

        return $args;
    }

    /**
     * Parse ispell output.
     *
     * @param string $output
     *
     * @return Issue[]
     *
     * @since 1.6
     */
    protected function parseOutput(string $output): array
    {
        $lines = explode(PHP_EOL, $output);
        $issues = [];
        $lineNo = 1;
        foreach ($lines as $line) {
            $line = trim($line);
            if ('' === $line) {
                // Go to the next line
                $lineNo++;
                continue;
            }
            switch ($line[0]) {
                case '#':
                    $parts = explode(' ', $line);
                    $word = $parts[1];
                    $issue = new Issue($word);
                    $issue->line = $lineNo;
                    $issue->offset = trim($parts[2]);
                    $issues [] = $issue;
                    break;
                case '&':
                    $matches = [];
                    $pattern = '/^& (?<original>[^\s]+) \d+ (?<offset>\d+): (?<suggestions>.*)$/';
                    if (1 === preg_match($pattern, $line, $matches)) {
                        $word = $matches['original'];
                        $issue = new Issue($word);
                        $issue->line = $lineNo;
                        $issue->offset = $matches['offset'];
                        $issue->suggestions = explode(', ', $matches['suggestions']);
                        $issues[] = $issue;
                    }
                    break;
            }
        }

        return $issues;
    }

    /**
     * Return path to folder with bundled ispell dictionaries.
     *
     * @return string
     *
     * @throws EnvironmentException
     * @throws LogicException
     * @throws RuntimeException
     */
    private function getDictionaryFolder(): string
    {
        if (null === $this->bundledDictFolder) {
            $binary = $this->getBinary();
            if (realpath($binary) === false) {
                $process = new Process(['which ' . $this->getBinary()]);
                $process->run();
                if (!$process->isSuccessful()) {
                    throw new EnvironmentException(
                        sprintf('Can not find full path of ispell: %s', $process->getErrorOutput())
                    );
                }
                $binary = trim($process->getOutput());
            }
            $this->bundledDictFolder = dirname($binary, 2) . '/lib/ispell';
        }

        return $this->bundledDictFolder;
    }
}
