<?php

namespace Toolkit;

class File // FileUtils
{
    public static function rename($oldname, $newname)
    {
        rename($oldname, $newname);
    }

    public static function delete($filename)
    {
        unlink($filename);
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

    public static function expired($filename, $ttl)
    {
        return (file_exists($filename) && time() - filemtime($filename) > $ttl);
       #return (file_exists($filename) && time() > strtotime($ttl, filemtime($filename)));
    }

    public static function notExpired($filename, $ttl)
    {
        return (file_exists($filename) && time() - filemtime($filename) < $ttl);
       #return (file_exists($filename) && time() < strtotime($ttl, filemtime($filename)));
    }

    /**
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
}
