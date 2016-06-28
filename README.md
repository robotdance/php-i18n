# PHP-I18n

[![Code Climate](https://codeclimate.com/github/robotdance/php-i18n/badges/gpa.svg)](https://codeclimate.com/github/robotdance/php-i18n)
[![Test Coverage](https://codeclimate.com/github/robotdance/php-i18n/badges/coverage.svg)](https://codeclimate.com/github/robotdance/php-i18n/coverage)
[![Issue Count](https://codeclimate.com/github/robotdance/php-i18n/badges/issue_count.svg)](https://codeclimate.com/github/robotdance/php-i18n)
[![Travis build](https://travis-ci.org/robotdance/php-i18n.svg?branch=master)](https://travis-ci.org/robotdance/php-i18n.svg?branch=master)

PHP-I18n is a simple I18N and L10n library.

## Setup

PHP-I18n uses [Composer](http://getcomposer.org) as dependency management tool.

`$ composer install`

## Use

Create a folder called `config/locales` at your app/module, and put your locale YAML files there.
(an example of valid YAML file can found in the source). Then call `I18n::t`, in one of the ways below.

### Simple key/value lookup
```php
$translated = I18n::t('example_message');
```

### Locale override
```php
$translated = I18n::t('example_message', [], 'pt_BR');
```

### Injecting arguments
```php
$translated = I18n::t('hello_message', ['user' => 'Bob']); // 'Hello Bob'
```
**Note about formatting** currently the library does not apply formatting to arguments.

### Localisation

Create a section 'l10n' on your locale file and follow the source standards.

```
$value = I18n::l(true);    // 'verdadeiro'
$value = I18n::l(123.45); // '123,45'
```

## Running tests

`$ ./bin/phpunit`

## Contribute

Fork, write tests, code, submit pull request. Coverage must remains at 100%.

## References

[PHP the right way](http://www.phptherightway.com)
[Fixing PHP errors automatically](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically)
[PHP SPL Exceptions](http://www.php.net/manual/en/spl.exceptions.php)
[What exception subclasses are buit in](http://stackoverflow.com/questions/10838257/what-exception-subclasses-are-built-into-php)
