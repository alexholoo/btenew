<?php

namespace Utility;

class Str
{
    public static function contains($haystack, $needle, $offset = 0)
    {
        return strpos($haystack, $needle, $offset) !== false;
    }

    public static function startsWith($haystack, $needle)
    {
         $length = strlen($needle);
         return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * Escape special XML characters
     * @return string with escaped XML characters
     */
    public static function escapeXML($str)
    {
        $from = array( "&", "<", ">", "'", "\"");
        $to = array( "&amp;", "&lt;", "&gt;", "&#039;", "&quot;");
        return str_replace($from, $to, $str);
    }

    /*
    public static function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    public static function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }
    */
}
