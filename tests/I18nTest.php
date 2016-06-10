<?php

namespace robotdance;

use robotdance\I18n;

class I18nTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        putenv('ENVIRONMENT=test');
        $this->changeLocale('en-US', 'en_US');
    }

    public function changeLocale($headerLocale, $phpLocale)
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = "Accept-Language: $headerLocale";
        \Locale::setDefault($phpLocale);
    }


    public function testValidKey()
    {
        $value = I18n::t('test_message');
        $this->assertNotEmpty($value);
    }

    public function testMissingKey()
    {
        $value = I18n::t('welcome_user');
        $this->assertEquals('welcome user', $value);
    }

    public function testValidParameters()
    {
        $value = I18n::t('hello_user', ['user'=>'Bob']);
        $this->assertEquals($value, 'Hello Bob');
    }

    public function testTranslation()
    {
        \Locale::setDefault('pt_BR');
        $value = I18n::t('hello_user', ['user'=>'Roberto']); // en_US
        $this->assertEquals($value, "Olá Roberto");
    }

    public function testLocaleChange()
    {
        $locale = I18n::getLocale();
        $this->assertEquals($locale, "en_US");
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'Accept-Language: pt-BR';
        \Locale::setDefault('pt_BR');
        $locale = I18n::getLocale();
        $this->assertEquals($locale, "pt_BR");
    }

    public function testAlias(){
        $value = I18n::translate('test_message');
        $this->assertNotEmpty($value);
    }

    public function testToAssociativeSyntax()
    {
        $value = I18n::toAssociativeSyntax("a");
        $this->assertEquals("['a']", $value);

        $value = I18n::toAssociativeSyntax("a[0]");
        $this->assertEquals("['a'][0]", $value);

        $value = I18n::toAssociativeSyntax("a.b");
        $this->assertEquals("['a']['b']", $value);

        $value = I18n::toAssociativeSyntax("a.b[0]");
        $this->assertEquals("['a']['b'][0]", $value);

        $value = I18n::toAssociativeSyntax("a.b.c");
        $this->assertEquals("['a']['b']['c']", $value);

        $value = I18n::toAssociativeSyntax("a[0].b[1].c[2]");
        $this->assertEquals("['a'][0]['b'][1]['c'][2]", $value);
    }

    public function testTraverseOk()
    {
        $array = ['a' => [1, 2], 'b' => ['c' => 3, 'd' => ['e' => 4, 'f' => 5]]];
        $this->assertEquals(4, I18n::traverse($array, 'b.d.e'));
    }

    public function testTraverseDefault()
    {
        $array = ['a' => [1, 2], 'b' => ['c' => 3, 'd' => ['e' => 4, 'f' => 5]]];
        $this->assertEquals(4, I18n::traverse($array, 'x.y', 4));
    }

    public function testL10nBoolean()
    {
        $this->assertEquals("False", I18n::l(false));
        $this->assertEquals("True", I18n::l(true));

        $this->changeLocale('pt-BR', 'pt_BR');
        $this->assertEquals("Falso", I18n::l(false));
        $this->assertEquals("Verdadeiro", I18n::l(true));

    }

    public function testL10nNumber()
    {
        $this->assertEquals('1,234.560', I18n::l(1234.56));

        $this->changeLocale('pt-BR', 'pt_BR');
        $this->assertEquals('1.234,560', I18n::l(1234.56));
    }

    public function testL10nArray(){
        $array = [1, 2, 3];
        $this->assertEquals('1, 2 and 3', I18n::l($array));

        $this->changeLocale('pt-BR', 'pt_BR');
        $this->assertEquals('1, 2 e 3', I18n::l($array));
    }
}
