<?php
/**
 * Simple I18n class for string messages
 */

namespace robotdance;

use robotdance\Config;
use robotdance\Arguments;
use Symfony\Component\Yaml\Yaml;

/**
 * Simple I18n class for string messages
 */
abstract class I18n
{
    /** word separator for yaml keys */
    const YAML_WORD_SEPARATOR = '_';

    /** locales path (relative to client app root directory) */
    const LOCALES_PATH = './config/locales';

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
        $yml = Yaml::parse(file_get_contents(self::LOCALES_PATH . "/$locale.yml"));
        $defaultMessage = strtr($key, [self::YAML_WORD_SEPARATOR => " "]);
        $message = self::traverse($yml, "$locale.$key", $defaultMessage);
        $injectedMessage = self::injectArguments($message, $args);
        return $injectedMessage;
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
     * Returns a string with localized data
     * @param $data Mixed The data to be localized/formatted
     * @param $locale String Locale overriding
     * @return String The localized data
     */
    public static function l($data, $locale = null)
    {
        if ($locale === null) {
            $locale = self::getLocale();
        }
        $yml = yaml_parse_file(self::LOCALES_PATH . "/$locale.yml");
        $applyFormatting = true;

        if (is_bool($data)){
            $key = ($data == true) ? 'truthy': 'falsy';
            $key = "$locale.l10n.boolean.$key";
            $value = self::traverse($yml, $key);
        } elseif (is_numeric($data)) {
            $precision = self::traverse($yml, "$locale.l10n.number.precision", '2');
            $delimiter = self::traverse($yml, "$locale.l10n.number.delimiter", '.');
            $separator = self::traverse($yml, "$locale.l10n.number.separator", ',');
            $value = number_format($data, $precision, $delimiter, $separator);
        } elseif (is_array($data)) {
            $conjunction = self::traverse($yml, "$locale.l10n.array.conjunction", ' and ');
            $pause = self::traverse($yml, "$locale.l10n.array.pause", ', ');
            $value = implode($pause, $data);
            $value = preg_replace('/(,\s)(\w+)$/', "$conjunction$2", $value);
        }
        return $value;
    }

    /**
     * Alias of self::l
     * @see self::l
     */
    public static function localize($str, $locale = null)
    {
        return self::l($str, $locale);
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
     * Accesses array element using a JQ or YAML dot syntax
     *
     * Examples:
     * $array = ['a' => [1, 2], 'b' => ['c' => 3, 'd' => [4, 5]]];
     * $path  = 'b.d[1]';
     * $value = traverse(&$array, $path); // => 5
     * @param $array Any array
     * @param $dotPath JQ or YAML dot syntax
     * @param $default Default value
     * @return element value
     */
    public static function traverse(&$array, $dotPath, $default = '')
    {
        $arrayPath = self::toAssociativeSyntax($dotPath);
        $cmd = "\$value = isset(\$array$arrayPath) ? \$array$arrayPath : '$default';";
        eval($cmd);
        return $value;
    }

    /**
     * Converts a jq/yaml path into associative array syntax
     *
     * Examples:
     * a    => ['a']
     * a.b  => ['a']['b']
     * a[0] => ['a'][0]
     *
     * @param $dotSyntax String containing a YAML or JQ dot access syntax
     * @return String containing associative access syntax
     */
    public static function toAssociativeSyntax($dotPath)
    {
        // please optimize me if you can
        $result = $dotPath;
        $result = preg_replace("/(\w+)(\['?\w+'?\])\.?/", "['$1']$2", $result);
        $result = preg_replace("/(\w+)$/", "['$1']", $result);
        $result = preg_replace("/^(\w+)\./", "['$1']", $result);
        $result = preg_replace("/(\w+)\./", "['$1']", $result);
        return $result;
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
