<?php

namespace Desidus\Rudder;

class Storage {

    /**
     * Storage folder
     * @var String
     */
    public static $path = 'storage';

    /**
     * path delle routes
     */
    public static function path($p = '')
    {
        return App::appPath() . '/' . self::$path . '/' . trim($p, '/');
    }

    /**
     * Salva i dati in un file
     */
    public static function save($name, $data, $params = [])
    {
        $file = [
            'timestamp' => time(),
            'content' => $data
        ];
        $file = array_merge($file, $params);
        file_put_contents(self::path($name), json_encode($file));
    }

    /**
     * Ritorna dati dalvati
     */
    public static function get($name)
    {
        $name = self::path($name);

        if(file_exists($name))
            return json_decode(file_get_contents($name), true);
    }

    /**
     * Ritorna dati in un file
     */
    public static function getContent($name)
    {
        return self::get($name)['content'];
    }
}