<?php

namespace Mekras\Speller\Examples;

use Mekras\Speller\Aspell\Aspell;
use Mekras\Speller\Exception\PhpSpellerException;
use Mekras\Speller\Hunspell\Hunspell;
use Mekras\Speller\Ispell\Ispell;
use Mekras\Speller\Source\FileSource;
use Mekras\Speller\Source\HtmlSource;
use Mekras\Speller\Source\IconvSource;
use Mekras\Speller\Source\XliffSource;

require __DIR__ . '/../vendor/autoload.php';

function showHelp()
{
    echo <<<EOT
php-speller example.

Usage: php spellcheck.php [options]
Options:
    -b <backend>    Backend to use: aspell, hunspell (default) or ispell.
    -E <encoding>   Dictionary internal encoding.
    -e <encoding>   Source text encoding (default UTF-8).
    -f <format>     Treat source as: html, xliff. 
    -i <filename>   Read text from a given file (default is STDIN).
    -l <languages>  Comma separated list of source text languages (default is system locale).
EOT;
}

$options = getopt('b:E:e:f:i:l:');

/* Choose backend. */
if (array_key_exists('b', $options)) {
    switch ($options['b']) {
        case 'aspell':
            $speller = new Aspell();
            break;
        case 'ispell':
            $speller = new Ispell();
            break;
        case 'hunspell':
            $speller = new Hunspell();
            break;
        default:
            fprintf(STDERR, "Invalid backend: %s\n", $options['b']);
            exit(-1);
    }
} else {
    $speller = new Hunspell();
}

/* Source text encoding */
$encoding = 'UTF-8';
if (array_key_exists('e', $options)) {
    $encoding = $options['e'];
}

/* Text source. */
$filename = 'php://stdin';
if (array_key_exists('i', $options)) {
    $filename = $options['i'];
}
$source = new FileSource($filename, $encoding);

/* Text source format. */
if (array_key_exists('f', $options)) {
    switch ($options['f']) {
        case 'html':
            $source = new HtmlSource($source);
            break;
        case 'xliff':
            $source = new XliffSource($source);
            break;
        default:
            fprintf(STDERR, "Invalid format: %s\n", $options['f']);
            exit(-1);
    }
}

/* Source language. */
$languages = [];
if (array_key_exists('l', $options)) {
    $languages = explode(',', $options['l']);
}

/* Dictionary encoding */
if (array_key_exists('E', $options)) {
    $source = new IconvSource($source, $options['E']);
}

try {
    $issues = $speller->checkText($source, $languages);
} catch (PhpSpellerException $e) {
    fprintf(STDERR, $e->getMessage() . PHP_EOL);
    exit(-1);
}
foreach ($issues as $issue) {
    print_r($issue);
}
