<?php

namespace Desidus\Rudder;

class View
{
    /**
     * Cartella contenente le views
     * @var String
     */
    private static $path = 'resources/views/';

    /**
     * path delle view
     */
    public static function path()
    {
        return App::appPath() . '/' . self::$path;
    }

    /**
     * Ritorna una view
     */
    public static function make($name, $data = [])
    {
        $file = self::path() . $name;

        if(file_exists($file)) {
            ob_start();
            extract($data, EXTR_SKIP);
            include($file);
            $file_content = ob_get_contents();
            ob_end_clean();
            return $file_content;
        }
    }
}