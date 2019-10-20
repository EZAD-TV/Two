<?php


namespace Two;


use Two\Container\Container;

/**
 * Global factory container / dependency manager. Classes registered with this will create new instances every
 * time get() is called. If you're planning on creating thousands of objects with a factory, consider using
 * a service with a simple factory method, it should be faster.
 *
 * @package Two
 */
class Factory extends Service
{
    /**
     * @var Container
     */
    private static $factoryContainer;

    /**
     * Get the inner container.
     *
     * @return Container
     */
    public static function getContainer()
    {
        return static::$factoryContainer ?? (static::$factoryContainer = new Container(true));
    }

    /**
     * @param string $key Class name or alias.
     * @param string $name If using a class, this is the optional configuration name.
     * @param array $override Do not be surprised if this parameter goes away.
     * @return object
     */
    public static function get(string $key, string $name = '', array $override = [])
    {
        return static::getContainer()->get($key, $name, $override);
    }
}