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
}
