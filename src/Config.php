<?php
/**
 * Manages configuration per environment and enforces existence of configurations
 */

namespace robotdance;

/**
 * Manages configuration per environment and enforces existence of configurations
 *
 * Conventions
 *
 * PHP calls Apache variables as 'environment variables', through the use of getEnv and putEnv functions.
 *
 * The environment (development, production, test) is set by an Apache variable,
 * usually defined in /etc/httpd/conf/httpd.conf or /etc/apache2/httpd.conf.
 *
 * Other configurations will be found at config.yml inside each composer package
 */
class Config
{
    private static $configFile = './config/config.yml';

    /**
     * Sets where to look for the configuration file.
     * Defaults to './config.yml'
     * @param $path the full path
     * @return String returns the same value if success
     */
    public static function setConfigFile($path)
    {
        if (!(is_file($path) && is_readable($path))) {
            throw new \InvalidArgumentException("File not found or without reading permission: $path");
        }
        self::$configFile = $path;
        return self::$configFile;
    }

    /**
     * @param  $key
     * @return String Value
     */
    public static function getEnvVar($key)
    {
        $value = getenv($key);
        if ($value == false) {
            throw new \InvalidArgumentException("Environment variable '$key' not found.");
        }
        return $value;
    }

    /**
     * Return configuration value
     * @param  $key
     * @return String Value
     */
    public static function get($key)
    {
        $env = self::getEnvVar('ENVIRONMENT');
        $yml = yaml_parse_file(self::$configFile);
        if (is_array($yml[$key]) && array_key_exists($env, $yml[$key])) {
            return $yml[$key][$env];
        } else {
            return $yml[$key];
        }
    }
}
