<?php

namespace Toolkit;

class Template
{
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
