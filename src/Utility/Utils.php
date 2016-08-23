<?php

namespace Utility;

class Utils
{
    public static function unzip($zipfile)
    {
         $zip = new ZipArchive;
         $res = $zip->open($zipfile);
         if ($res === TRUE) {
             $zip->extractTo(dirname($zipfile));
             $zip->close();
             return true;
         }
         return false;
    }

    public static function deleteOldFiles($dir, $interval = strtotime('-10 days'))
    {
        // cycle through all files in the directory
        foreach (glob($dir."*") as $file) {
            // if file is too old then delete it ***/
            if (filemtime($file) <= $interval) {
                //unlink($file);
                echo $file, "\n";
            }
        }
    }

    public static function renderView($__file, $__data)
    {
        ob_start();
        extract($__data);
        include("views/$__file.tpl");
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public static function render($__file, $__data, $__layout = '')
    {
        $content = renderView($__file, $__data);
        if (empty($__layout))
            $__layout = 'layout';
    //  extract($__data);
        include("views/$__layout.tpl");
    }

    public static function template($__file, $__data, $__layout = '')
    {
        ob_start();
        extract($__data);
        include("views/$__file.tpl");
        $content = ob_get_contents();
        ob_end_clean();

        if (empty($__layout)) {
            $__layout = 'layout';
        }
        include("views/$__layout.tpl");
    }
}
