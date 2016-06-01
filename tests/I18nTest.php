<?php

namespace robotdance;

use robotdance\I18n;

class I18nTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        putenv('ENVIRONMENT=test');
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'Accept-Language: en-US';
        \Locale::setDefault('en_US');
    }

    /**
     * Must return a string
     */
    public function testValidKey()
    {
        $value = I18n::t('test_message');
        $this->assertNotEmpty($value);
    }

    /**
     * Must fall back to key without underscores
     */
    public function testMissingKey()
    {
        $value = I18n::t('welcome_user');
        $this->assertEquals($value, 'welcome user');
    }

    /**
     * Test is parameter is replaced at right place
     */
    public function testValidParameters()
    {
        $value = I18n::t('hello_user', ['user'=>'Bob']);
        $this->assertEquals($value, 'Hello Bob');
    }

    /**
     * If we change locale, then the string must change too
     */
    public function testTranslation()
    {
        \Locale::setDefault('pt_BR');
        $value = I18n::t('hello_user', ['user'=>'Roberto']); // en_US
        $this->assertEquals($value, "Olá Roberto");
    }

    /**
     * Must reflect change
     */
    public function testLocaleChange()
    {
        $locale = I18n::getLocale();
        $this->assertEquals($locale, "en_US");
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'Accept-Language: pt-BR';
        \Locale::setDefault('pt_BR');
        $locale = I18n::getLocale();
        $this->assertEquals($locale, "pt_BR");
    }
}
