<?php

namespace Toolkit;

class Files
{
    public static function join(array $files, $target)
    {
        $dest = fopen($target, "w+");

        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }

            $src = fopen($file, "r");
            while (!feof($src)) {
                fwrite($dest, fgets($src));
            }
            fclose($src);
            fwrite($dest, "\n"); // usually last line doesn't have a newline
        }

        fclose($dest);
    }

    public static function deleteOld($dir, $interval)
    {
        if (!$interval) {
            $interval = strtotime('-10 days');
        }

        // cycle through all files in the directory
        foreach (glob($dir."*") as $file) {
            // if file is too old then delete it ***/
            if (filemtime($file) <= $interval) {
                //unlink($file);
                echo $file, "\n";
            }
        }
    }

    /**
     * archiveFiles('../app/logs/*.xml');
     */
    public static function archive($pattern)
    {
        $files = glob($pattern);

        foreach ($files as $file) {
            $date = date('Y-m-d', filemtime($file));
            $dir = dirname($file).'/archive/'.$date;

            if (!file_exists($dir)) {
                @mkdir($dir, 0777, true);
            }

            rename($file, $dir.'/'.basename($file));
        }
    }
}
