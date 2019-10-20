<?php


namespace Two\Container;


use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

class Container
{
    private $factory = false;
    private $configs = [];
    private $defaults = [];
    private $aliases = [];
    private $instances = [];

    /**
     * Container constructor.
     * @param bool $factory
     */
    public function __construct(bool $factory)
    {
        $this->factory = $factory;
    }

    public function get(string $key, string $name, array $override)
    {
        if ( isset($this->aliases[$key]) ) {
            $id = $this->aliases[$key];
        } else {
            if ( $name === '' && isset($this->defaults[$key]) ) {
                $name = $this->defaults[$key];
            }
            $id = "$key!$name";
        }

        if ( !$this->factory && isset($this->instances[$id]) ) {
            return $this->instances[$id];
        }

        $config = $this->configs[$id];
        $class = $config['class'];

        try {
            $ref = new ReflectionClass($class);
        } catch ( ReflectionException $e ) {
            throw new InvalidArgumentException("'$class' not found", $e->getCode(), $e);
        }

        $parameters = $ref->getConstructor()->getParameters();
        $givenParams = $config['params'];
        if ( $this->factory && $override ) {
            $givenParams = array_merge($givenParams, $override);
        }

        $arguments = [];
        foreach ( $parameters as $parameter ) {
            $paramName = $parameter->name;

            if ( !isset($givenParams[$paramName]) ) {
                if ( $parameter->isOptional() ) {
                    $arg = $parameter->getDefaultValue();
                } else {
                    throw new InvalidArgumentException("'$paramName' is required for '$class' but was not given");
                }
            } else {
                $arg = $givenParams[$paramName];
            }

            // handle injecting services recursively. The param name must be a full class name string or alias, or
            // a 2-element array with class name and key.
            $paramClass = $parameter->getClass();
            if ( $paramClass !== null ) {
                if ( is_object($arg) ) {
                    // set directly
                } else if ( is_string($arg) ) {
                    $arg = $this->get($arg, '', []);
                } else if ( is_array($arg) && count($arg) === 2 ) {
                    $arg = $this->get($arg[0], $arg[1], []);
                } else {
                    throw new InvalidArgumentException("'$paramName' requires a string or 2 element array");
                }
            }

            $arguments[] = $arg;
        }

        $instance = $ref->newInstanceArgs($arguments);

        if ( $config['function'] ) {
            call_user_func($config['function'], $instance);
        }

        if ( !$this->factory ) {
            $this->instances[$id] = $instance;
        }
        return $instance;
    }

    public function alias(string $alias, string $class, string $name)
    {
        $this->aliases[$alias] = "$class!$name";
    }

    public function hasAlias($alias)
    {
        return isset($this->aliases[$alias]);
    }

    public function setDefault(string $class, string $name)
    {
        $this->defaults[$class] = $name;
    }

    public function set(string $class, string $name, array $params, ?Closure $func)
    {
        $id = "$class!$name";

        $this->configs[$id] = [
            'class' => $class,
            'params' => $params,
            'function' => $func,
        ];

        if ( !isset($this->defaults[$class]) ) {
            $this->defaults[$class] = $name;
        }
    }
}