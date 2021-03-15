# Changelog

## 2.1.4 - 2021-03-15
### Changed
- Add support for PHP 8
### Removed
- Removed `getMessage` from `PhpSpellerException` as it already declared from `Throwable`

## 2.1.3 - 2020-09-23
### Fixed
- Fix incorrect escaping of hunspell command (#29)

## 2.1.2 - 2020-08-20
### Fixed
- HtmlFilter raises Error with malformed HTML tags (#27)

## 2.1.1 - 2020-06-18
### Fixed
- Fixed an issue where `:` (colon) made the suggestions break for swedish as its a valid text sign (#25)

## 2.1 - 2020-05-06
### Fixed
- Fixed issue with removed and deprecated methods for `symfony/process:^4.0`

### Changed
- Add requirement for `php:^7.2`
- Changed minimum requirement for `symfony/process` to `^4.4`

## 2.0.4 - 2020-04-30
### Added
- Added support for `symfony/process:^5.0` to work with laravel 7 (thanks [@](https://github.com/ibarrajo) [#21](https://github.com/mekras/php-speller/pull/21))

## 2.0.3 - 2020-04-28
### Fixed
- Resolve an issue with malformed html input (thanks to [@rouliane](https://github.com/rouliane) [#20](https://github.com/mekras/php-speller/pull/20))

## 2.0.2 - 2019-01-25
### Fixed
- backward support for `php:^7.1 and `symfony/process: ^3.4`

## 2.0.1 - 2019-01-18
### Fixed
- Fixed support for hunspell paths on windows OS

## 2.0 - 2019-01-17
### Added
- Add custom dictionary support for aspell

### Changed
- Raise PHP requirement to 7.2

### Removed
- Dropped `@deprecated` interfaces

## 1.7.2 - 2017-04-30
### Fixed
- HtmlFilter: `<script>` content should be filtered out.
- HtmlFilter: only for "keywords" and "description" meta tags "content" attr should be treated as
  string.

## 1.7.1 - 2017-04-29
### Fixed
- HtmlFilter: meta tags with http-equiv should be filtered out.

## 1.7 - 2017-03-22
### Fixed
- #6: Failed to execute "hunspell -i UTF-8 -a": Can't open affix or dictionary files for dictionary
  named "default".
- FileSource throws SourceException when using "php://stdin".

### Changed
- HtmlSource should throw SourceException on invalid HTML.

## 1.6 - 2017-03-12
### Added
- Aspell — aspell backend.
- Ispell — ispell backend.
- IconvSource — converts text encoding using iconv.
- MetaSource — base class for meta sources.
- EncodingAwareSource — text source with specified encoding.
- ExternalSpeller — base class for external program adapters.
- Own exceptions.

### Changed
- Hunspell class derived from new Ispell class.
- All sources now implement EncodingAwareSource.
- HtmlSource and XliffSource derived from MetaSource.

## 1.5.1 - 2017-03-11
### Fixed
- HtmlSource: only for "keywords" and "description" meta tags "content" attr should be treated as
  user visible text.

## 1.5 - 2017-03-11
### Added
- HtmlSource.

## 1.4.1 - 2016-08-02
### Fixed
- #2: Word suggestions with space splits up

## 1.4 - 2016-04-19
### Deleted
- Dropped PHP 5.4 support.

### Added
- PHP 7 support.
- Symfony 3.x support.

## 1.3.1 - 2015-05-13
### Changed
- Fixed HTML entities filtering in HtmlFilter

## 1.3 - 2015-05-13
### Added
- HtmlFilter added.

## 1.2 - 2015-05-12
### Added
- New feature: Filters.
- FileSource.
- XliffSource for XLIFF translation files.

### Changed
- Forced UTF-8 input encoding for Hunspell.

## 1.1 - 2015-05-08
### Fixed
- Fixed invalid shell arguments escaping.
### Added
- LanguageMapper now supports manual setting of preferred mappings.

## 1.0 - 2015-05-08
Initial release.
