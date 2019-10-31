<?php


namespace Two\Util;


class Arrays
{
    /**
     * Merges arrays recursively.
     *
     * @param array $first
     * @param array $second
     * @param bool $exclamationNoRecurse If true, and the second array has a key starting with "!", it will overwrite
     *                                   the first array's key completely with no deep merging.
     * @return array
     */
    public static function mergeRecursive(array $first, array $second, bool $exclamationNoRecurse = false): array
    {
        $final = $first;

        foreach ( $second as $key => $value ) {
            $noRecurse = $exclamationNoRecurse && $key[0] === '!';
            if ( $noRecurse ) {
                $key = substr($key, 1);
            }

            if ( isset($final[$key]) && is_array($final[$key]) && is_array($value) && !$noRecurse ) {
                $final[$key] = self::mergeRecursive($final[$key], $value);
            } else {
                $final[$key] = $value;
            }
        }

        return $final;
    }

    public static function deepGet(array $array, string $key, $default = null)
    {
        if (strpos($key, '.') === false) {
            return $array[$key] ?? $default;
        }

        $parts = explode('.', $key);
        $value = $array;
        while (count($parts) > 0) {
            $part = array_shift($parts);
            if (!isset($value[$part])) {
                return $default;
            }
            $value = $value[$part];
        }

        return $value;
    }

    public static function deepSet(array &$array, string $key, $value)
    {
        if ( strpos($key, '.') === false ) {
            $array[$key] = $value;
            return;
        }

        $split = explode('.', $key);
        $first = array_shift($split);
        if ( !isset($array[$first]) ) {
            $array[$first] = [];
        }
        self::deepSet($array[$first], implode('.', $split), $value);
    }
}