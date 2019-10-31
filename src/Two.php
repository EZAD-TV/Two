<?php


namespace Two;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Two\Config\Config;
use Two\Http\Input\Input;
use Two\Http\Session;

class Two
{
    /**
     * @var Config
     */
    private static $config;

    /**
     * @var Request
     */
    private static $request;

    /**
     * @var Input
     */
    private static $input;

    /**
     * @var Session
     */
    private static $session;

    /**
     * @var EventDispatcherInterface
     */
    private static $bus;

    /**
     * @var string
     */
    private static $mode;
    const CLI = 'cli';
    const HTTP = 'http';

    public static function url()
    {
        return '';
    }

    /**
     * @param Request $request
     */
    public static function setRequest(Request $request): void
    {
        self::$request = $request;
    }

    /**
     * @return Request
     */
    public static function request(): Request
    {
        return self::$request;
    }

    /**
     * @param Input $input
     */
    public static function setInput(Input $input): void
    {
        self::$input = $input;
    }

    /**
     * @return Input
     */
    public static function input(): Input
    {
        return self::$input;
    }

    /**
     * @return EventDispatcherInterface
     */
    public static function bus(): EventDispatcherInterface
    {
        return self::$bus;
    }

    /**
     * @param EventDispatcherInterface $bus
     */
    public static function setBus(EventDispatcherInterface $bus): void
    {
        self::$bus = $bus;
    }

    /**
     * @return Session
     */
    public static function session(): Session
    {
        return self::$session;
    }

    /**
     * @param Session $session
     */
    public static function setSession(Session $session): void
    {
        self::$session = $session;
    }

    /**
     * @return string
     */
    public static function mode(): string
    {
        return self::$mode;
    }

    /**
     * @param string $mode
     */
    public static function setMode(string $mode): void
    {
        self::$mode = $mode;
    }

    /**
     * Load configuration from multiple folders containing .php files. Later folders in the array will override
     * configuration defined in earlier folders.
     *
     * Config files should return arrays. You can use the $env variable in config files to refer to server
     * environment variables or properties in the .env file.
     *
     * @param array $configFolders
     * @param array $env
     */
    public static function loadConfig(array $configFolders, array $env)
    {
        self::$config = new Config($configFolders, $env);
    }

    /**
     * Retrieve a config option. Use "." to split between groups and sub-keys in the configuration array.
     * So if you have a "db.php" config file, inside of which you have ['local' => ['password' => 'foo']],
     * you can access "foo" by doing Two::config('db.local.password').
     *
     * Two::config('db') would give you the entire array from db.php.
     * Two::config('db.local') would give you ['password' => 'foo'].
     *
     * @param string $key
     * @param null $default
     * @return array|mixed|null
     */
    public static function config(string $key, $default = null)
    {
        return self::$config->get($key, $default);
    }
}