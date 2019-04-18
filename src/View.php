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

        $data = array_merge($data, [
            'microdata' => SEO::resolve()
        ]);
        
        if(file_exists($file)) {
            ob_start();
            extract($data, EXTR_SKIP);
            include($file);
            $file_content = ob_get_contents();
            ob_end_clean();
            //return self::sanitize_output($file_content);
            return $file_content;
        }
    }

    public static function sanitize_output($buffer) 
    {
        if (!App::inProduction())
        {
            return $buffer;
        }

        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );
    
        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );
    
        $buffer = preg_replace($search, $replace, $buffer);
    
        return $buffer;
    }
}