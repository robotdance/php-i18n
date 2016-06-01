<?php
/**
 * Simple I18n class for string messages
 */

namespace robotdance;

use robotdance\Config;
use robotdance\Arguments;

/**
 * Simple I18n class for string messages
 */
abstract class I18n
{
    /**
     * Returns a message according to locale
     * @param $key String Name of the key in I18n YAML file
     * @param $args Array Associative array string=>mixed for arguments replacement
     * @param $locale String Current locale overriding
     * @return String The translated message with arguments
     */
    public static function t($key, $args = [], $locale = null)
    {
        Arguments::validate($key, ['string']);
        if ($locale === null) {
            $locale = self::getLocale();
        }
        $yml = yaml_parse_file("./config/locales/$locale.yml");
        if (array_key_exists($locale, $yml) && array_key_exists($key, $yml[$locale])) {
            $message = $yml[$locale][$key];
            $value = self::injectArguments($message, $args);
        } else {
            $value = strtr($key, ["_" => " "]);
        }
        return $value;
    }

    /**
     * Alias of I18n::t
     * @see I18n::t
     */
    public static function translate($key, $args = [], $locale = null)
    {
        return self::t($key, $args, $locale);
    }

    /**
     * Returns the current locale
     * @return String PHP locale string
     */
    public static function getLocale()
    {
        $locale = Arguments::prioritize([\Locale::acceptFromHttp(self::safeHeader('HTTP_ACCEPT_LANGUAGE')),
                                         \Locale::getDefault(),
                                         Config::get('default_locale')]);
        return $locale;
    }

    /**
     * Returns a message with argument tokens replaced by array values
     * Given a string like 'Hello %{user}' and ['user' => 'Marcos],
     * the resulting string will be 'Hello Marcos', for example.
     * @param $string String a message
     * @param $args Array Associative array string=>value
     * @return String replaced string
     */
    private static function injectArguments($string, $args = [])
    {
        foreach ($args as $pattern => $replacement) {
            $string = preg_replace("/\%\{$pattern\}/", $replacement, $string);
        }
        return $string;
    }

    /**
     * Returns the header or null instead of generating a warning
     * @param $key String Key to lookup in $_SERVER
     * @param $defaultValue Default value if not found
     * @return String Header value, defaultValue or NULL if no defaultValue passed
     */
    private static function safeHeader($key, $defaultValue = null)
    {
        $value = $defaultValue;
        if (array_key_exists($key, $_SERVER)) {
            $value = $_SERVER[$key];
        }
        return $value;
    }
}
