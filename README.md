# php-speller

PHP spell check library.

[![Latest Stable Version](https://poser.pugx.org/mekras/php-speller/v/stable.png)](https://packagist.org/packages/mekras/php-speller)
[![License](https://poser.pugx.org/mekras/php-speller/license.png)](https://packagist.org/packages/mekras/php-speller)
[![Build Status](https://travis-ci.org/mekras/php-speller.svg?branch=master)](https://travis-ci.org/mekras/php-speller)
[![Coverage Status](https://coveralls.io/repos/mekras/php-speller/badge.png?branch=master)](https://coveralls.io/r/mekras/php-speller?branch=master)

Currently supported backends:

* [hunspell](http://hunspell.sourceforge.net/).

## Installation

With [Composer](http://getcomposer.org/):

    $ composer require mekras/php-speller:dev-master

## Usage

```php
use Mekras\Speller\Hunspell\Hunspell;
use Mekras\Speller\Source\StringSource;

$speller = new Hunspell();
$source = new StringSource('Tiger, tigr, burning bright');
$issues = $speller->checkText($source, ['en_GB', 'en']);

echo $issues[0]->word; // -> "tigr"
echo $issues[0]->line; // -> 1
echo $issues[0]->offset; // -> 7
echo implode(',', $issues[0]->suggestinons); // -> tiger, trig, tier, tigris, tigress
```

Get list of languages supported by backend:

```php
/** @var Mekras\Speller\Speller $speller */
print_r($speller->getSupportedLanguages());
```

## Hunspell

This backend uses hunspell program, so it should be installed in the system.

Path to binary can be set in constructor:

```php
$speller = new Hunspell('/usr/local/bin/hunspell');
```

You can set additional dictionary path:

```php
$speller = new Hunspell();
$speller->setDictionaryPath('/var/spelling/custom');
```

You can specify custom dictionaries to use:

```php
$speller = new Hunspell();
$speller->setDictionaryPath('/my_app/spelling');
$speller->setCustomDictionaries(['tech', 'titles']);
```
