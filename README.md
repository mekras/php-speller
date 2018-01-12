# php-speller

PHP spell check library.

[![Latest Stable Version](https://poser.pugx.org/mekras/php-speller/v/stable.png)](https://packagist.org/packages/mekras/php-speller)
[![License](https://poser.pugx.org/mekras/php-speller/license.png)](https://packagist.org/packages/mekras/php-speller)
[![Build Status](https://travis-ci.org/mekras/php-speller.svg?branch=master)](https://travis-ci.org/mekras/php-speller)
[![Coverage Status](https://coveralls.io/repos/mekras/php-speller/badge.png?branch=master)](https://coveralls.io/r/mekras/php-speller?branch=master)

Currently supported backends:

* [aspell](http://aspell.net/);
* [hunspell](http://hunspell.sourceforge.net/);
* [ispell](https://www.cs.hmc.edu/~geoff/ispell.html).

## Installation

With [Composer](http://getcomposer.org/):

    $ composer require mekras/php-speller

## Usage

1. Create a text source object from string, file or something else using one of the
   `Mekras\Speller\Source\Source` implementations (see [Sources](#Sources) below).
2. Create some speller instance (Hunspell, Ispell or any other implementation of the
   `Mekras\Speller\Speller`).
3. Execute `Speller::checkText()` method.

```php
use Mekras\Speller\Hunspell\Hunspell;
use Mekras\Speller\Source\StringSource;

$source = new StringSource('Tiger, tigr, burning bright');
$speller = new Hunspell();
$issues = $speller->checkText($source, ['en_GB', 'en']);

echo $issues[0]->word; // -> "tigr"
echo $issues[0]->line; // -> 1
echo $issues[0]->offset; // -> 7
echo implode(',', $issues[0]->suggestions); // -> tiger, trig, tier, tigris, tigress
```

You can list languages supported by backend:

```php
/** @var Mekras\Speller\Speller $speller */
print_r($speller->getSupportedLanguages());
```

See [examples](examples/) for more info. 

### Source encoding

For aspell, hunspell and ispell source text encoding should be equal to dictionary encoding. You can
use [IconvSource](#IconvSource) to convert source.

## Aspell

This backend uses aspell program, so it should be installed in the system.

```php
use Mekras\Speller\Aspell\Aspell;

$speller = new Aspell();
```

Path to binary can be set in constructor:

```php
use Mekras\Speller\Aspell\Aspell;

$speller = new Aspell('/usr/local/bin/aspell');
```

### Custom Dictionary

You can use a custom dictionary for aspell. The dictionary needs to be in the following format:

```
personal_ws-1.1 [lang] [words]
```

Where `[lang]` shout be the shorthand for the language you are using (e.g. `en`) and `[words]` is the count
of words inside the dictionary. **Beware** that there should no spaces at the end of words. Each word should be listed
in a new line.

```php
$aspell = new Aspell();
$aspell->setPersonalDictionary(new Dictionary('/path/to/custom.pws'));
```

### Important

- aspell allow to specify only one language at once, so only first item taken from
$languages argument in ``Ispell::checkText()``.


## Hunspell

This backend uses hunspell program, so it should be installed in the system.

```php
use Mekras\Speller\Hunspell\Hunspell;

$speller = new Hunspell();
```

Path to binary can be set in constructor:

```php
use Mekras\Speller\Hunspell\Hunspell;

$speller = new Hunspell('/usr/local/bin/hunspell');
```

You can set additional dictionary path:

```php
use Mekras\Speller\Hunspell\Hunspell;

$speller = new Hunspell();
$speller->setDictionaryPath('/var/spelling/custom');
```

You can specify custom dictionaries to use:

```php
use Mekras\Speller\Hunspell\Hunspell;

$speller = new Hunspell();
$speller->setDictionaryPath('/my_app/spelling');
$speller->setCustomDictionaries(['tech', 'titles']);
```

## Ispell

This backend uses ispell program, so it should be installed in the system.

```php
use Mekras\Speller\Ispell\Ispell;

$speller = new Ispell();
```

Path to binary can be set in constructor:

```php
use Mekras\Speller\Ispell\Ispell;

$speller = new Ispell('/usr/local/bin/ispell');
```

### Important

- ispell allow to use only one dictionary at once, so only first item taken from
$languages argument in ``Ispell::checkText()``.


## Sources

Sources — is an abstraction layer allowing spellers receive text from different sources like strings
or files.

### FileSource

Reads text from file.

```php
use Mekras\Speller\Source\FileSource;

$source = new FileSource('/path/to/file.txt');
```

You can specify file encoding:

```php
use Mekras\Speller\Source\FileSource;

$source = new FileSource('/path/to/file.txt', 'windows-1251');
```

### StringSource

Use string as text source.

```php
use Mekras\Speller\Source\StringSource;

$source = new StringSource('foo', 'koi8-r');
```

## Meta sources 

Additionally there is a set of meta sources, which wraps other sources to perform extra tasks.

### HtmlSource

Return user visible text from HTML.

```php
use Mekras\Speller\Source\HtmlSource;

$source = new HtmlSource(
    new StringSource('<a href="#" title="Foo">Bar</a> Baz')
);
echo $source->getAsString(); // Foo Bar Baz
```

Encoding detected via
[DOMDocument::$encoding](http://php.net/manual/en/class.domdocument.php#domdocument.props.encoding).

### IconvSource

This is a meta-source which converts encoding of other given source:

```php
use Mekras\Speller\Source\IconvSource;
use Mekras\Speller\Source\StringSource;

// Convert file contents from windows-1251 to koi8-r.
$source = new IconvSource(
    new FileSource('/path/to/file.txt', 'windows-1251'),
    'koi8-r'
);
```

### XliffSource
  
Loads text from [XLIFF](http://docs.oasis-open.org/xliff/xliff-core/v2.0/xliff-core-v2.0.html)
files.

```php
use Mekras\Speller\Source\XliffSource;

$source = new XliffSource(__DIR__ . '/fixtures/test.xliff');
```

## Source filters

Filters used internally to filter out all non text contents received from source. In order to save
original word location (line and column numbers) all filters replaces non text content with spaces.

Available filters:

* [StripAllFilter](src/Source/Filter/StripAllFilter.php) — strips all input text;
* [HtmlFilter](src/Source/Filter/HtmlFilter.php) — strips HTML tags.
