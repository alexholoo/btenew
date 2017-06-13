<?php

namespace Toolkit;

class File // FileUtils
{
    public static function exists($filename)
    {
        return file_exists($filename);
    }

    public static function rename($oldname, $newname)
    {
        return rename($oldname, $newname);
    }

    public static function delete($filename)
    {
        return unlink($filename);
    }

    public static function unzip($zipfile)
    {
         $zip = new \ZipArchive;
         $res = $zip->open($zipfile);
         if ($res === TRUE) {
             $zip->extractTo(dirname($zipfile));
             $zip->close();
             return true;
         }
         return false;
    }

    /**
     * $ttl can be in two formats
     * - number: for example, 3600, the interval in seconds
     * - string: '3 hours', '1 day', etc
     */
    public static function expired($filename, $ttl)
    {
        if (ctype_digit($ttl)) {
            return (!file_exists($filename) || time() - filemtime($filename) > $ttl);
        }
        return (!file_exists($filename) || time() > strtotime($ttl, filemtime($filename)));
    }

    /**
     * @see File::expired
     */
    public static function notExpired($filename, $ttl)
    {
        if (ctype_digit($ttl)) {
            return (file_exists($filename) && time() - filemtime($filename) < $ttl);
        }
        return (file_exists($filename) && time() < strtotime($ttl, filemtime($filename)));
    }

    /**
     * Rename the file by suffixing filename with timestamp
     *
     * Before:
     *   $filename = 'E:/BTE/purchase/shopping-cart.csv';
     *
     * After:
     *   $filename = 'E:/BTE/purchase/shopping-cart-20170106-141936.csv';
     */
    public static function backup($filename)
    {
        if (!file_exists($filename)) {
            return;
        }

        $path = pathinfo($filename);

        $dir   = $path['dirname'];
        $fname = $path['filename'].'-'.date('Ymd-His', filemtime($filename));
        $ext   = isset($path['extension']) ? $path['extension'] : '';
       #$ext   = $path['extension'] ?? ''; // php7+ only

        $newfile = "$dir/$fname.$ext";

        rename($filename, $newfile);
    }

    /**
     * Rename the file by suffixing filename with timestamp, then move the file
     * to archive/ folder
     *
     * Before:
     *   $filename = 'E:/BTE/purchase/shopping-cart.csv';
     *
     * After:
     *   $filename = 'E:/BTE/purchase/archive/shopping-cart-20170106-141936.csv';
     */
    public static function archive($filename)
    {
        if (!file_exists($filename)) {
            return;
        }

        $path = pathinfo($filename);

        $dir   = $path['dirname'] . '/archive';
        $fname = $path['filename'].'-'.date('Ymd-His', filemtime($filename));
        $ext   = $path['extension'] ?? ''; // php7+ only

        $newfile = "$dir/$fname.$ext";

        rename($filename, $newfile);
    }

    /**
     * Rename the file by suffixing filename with timestamp, it's useful for
     * web assets versioning.
     *
     * $filename = File::asset('/assets/css/style.css');
     */
    public static function asset($filename, $webroot = BASE_DIR.'/public')
    {
        if (!file_exists($webroot.$filename)) {
            return $filename;
        }

        $path = pathinfo($filename);

        $dir   = $path['dirname'];
        $fname = $path['filename'];
        $ftime = filemtime($webroot.$filename);
        $ext   = isset($path['extension']) ? $path['extension'] : '';

        $newfile = "$dir/$fname.$ftime.$ext";

        return $newfile;
    }
}
