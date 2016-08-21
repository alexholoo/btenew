<?php

namespace Utility;

class Arr
{
    public static function get($array, $key, $default = null)
    {
        if (isset($array[$key])) {
           return $array[$key];
        }

        $keys = explode('.', $key);
        $data = $array;

        while (null !== ($name = array_shift($keys))) {
            if (!isset($data[$name])) {
                return $default;
            }

            $data = $data[$name];
        }

        return $data;
    }

    /**
     * Checks  whether passed variable is numeric array
     *
     * @param mixed $var
     * @return TRUE if passed variable is an numeric array
     */
    public static function isNumericArray($var)
    {
        if (!is_array($var)) {
           return false;
        }
        $sz = sizeof($var);
        return ($sz===0 || array_keys($var) === range(0, sizeof($var) - 1));
    }

    /**
     * Checks  whether passed variable is an associative array
     *
     * @param mixed $var
     * @return TRUE if passed variable is an associative array
     */
    public static function isAssociativeArray($var)
    {
        return is_array($var) && array_keys($var) !== range(0, sizeof($var) - 1);
    }
}
