<?php


namespace Two\Text;


class InflectUtil
{
    public static function camelToSnake(string $camel)
    {
        // insert _ when moving from [a-z] to [A-Z]
        // OrderManager -> order_manager
        $alias = preg_replace('/([a-z])([A-Z])/', '$1_$2', $camel);

        // insert _ before last [A-Z], when 2 or more [A-Z]
        // SuperXMLGenerator -> super_xml_generator
        $alias2 = preg_replace_callback('/[A-Z]{2,}/', function($m) {
            return substr($m[0], 0, -1) . '_' . substr($m[0], -1);
        }, $alias);
        if ( $alias2 !== $alias ) {
            // detect crap like "OrderXML" -> "Order_XM_L"
            $alias = preg_replace('/([A-Z])_([A-Z])$/', '$1$2', $alias2);
        }

        return strtolower($alias);
    }

    public static function simpleClassName(string $className)
    {
        $slash = strrpos($className, '\\');
        return $slash === false ? $className : substr($className, $slash + 1);
    }
}