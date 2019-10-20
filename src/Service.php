<?php


namespace Two;


use Symfony\Component\Inflector\Inflector;
use Two\Container\Container;
use Two\Text\InflectUtil;

/**
 * Global service container / dependency manager. Classes registered with this will have their instances re-used.
 *
 * @package Two
 */
class Service
{
    /**
     * @var Container
     */
    private static $container;

    /**
     * Get the inner container.
     *
     * @return Container
     */
    public static function getContainer()
    {
        return static::$container ?? (static::$container = new Container(false));
    }

    /**
     * @param string $key Class name or alias.
     * @param string $name If using a class, this is the optional configuration name.
     * @return object
     */
    public static function get(string $key, string $name = '')
    {
        return static::getContainer()->get($key, $name, []);
    }

    /**
     * Create an alias for a class & configuration.
     *
     * @param string $alias
     * @param string $class
     * @param string $name
     */
    public static function alias(string $alias, string $class, string $name = '')
    {
        static::getContainer()->alias($alias, $class, $name);
    }

    /**
     * Set the default configuration for a class.
     *
     * @param string $class
     * @param string $name
     */
    public static function setDefault(string $class, string $name)
    {
        static::getContainer()->setDefault($class, $name);
    }

    /**
     * Register a service class.
     *
     * Valid ways to call the function:
     * Service::set(string $class, string $name, [array $parameters], [Closure $function]);
     * Service::set(string $class, array $parameters, [Closure $function]);
     * Service::set(string $class, Closure $function);
     *
     * This will also auto-create an alias with the "snake_case" version of the last part of the class name
     * after the namespace, followed by "_name" if name is given.
     *
     * So a class "App\TwilioHelper" with name "foo" would create an alias at "twilio_helper_foo".
     *
     * @param string $class
     * @param string $nameOrParamsOrFunction
     * @param null $paramsOrFunction
     * @param null $function
     */
    public static function set(string $class, $nameOrParamsOrFunction = '', $paramsOrFunction = null, $function = null)
    {
        $name = '';
        $params = [];
        $func = null;

        // Service::set(string $class, string $name, [array $parameters], [callable $function]);
        // Service::set(string $class, array $parameters, [callable $function]);
        // Service::set(string $class, callable $function);
        if ( is_string($nameOrParamsOrFunction) ) {
            $name = $nameOrParamsOrFunction;
            if ( is_array($paramsOrFunction) ) {
                $params = $paramsOrFunction;
                $func = $function;
            } else {
                $func = $paramsOrFunction;
            }
        } else if ( is_array($nameOrParamsOrFunction) ) {
            $params = $nameOrParamsOrFunction;
            $func = $paramsOrFunction;
        } else {
            $func = $nameOrParamsOrFunction;
        }

        /** @noinspection PhpParamsInspection */
        static::getContainer()->set($class, $name, $params, $func);

        // auto-alias. not 100% sure if a good idea.
        $alias = static::makeAlias($class, $name);
        if ( !static::getContainer()->hasAlias($alias) ) {
            static::alias($alias, $class, $name);
        }
    }

    public static function makeAlias(string $class, string $name = '')
    {
        $simpleName = InflectUtil::simpleClassName($class);
        $alias = InflectUtil::camelToSnake($simpleName);
        if ( $name ) {
            $alias .= '_' . strtolower($name);
        }

        return $alias;
    }
}