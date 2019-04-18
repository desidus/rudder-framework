<?php

namespace Desidus\Rudder;

class Cache 
{

    /**
     * Setta una variabile nella cache
     *
     * @param string $name
     * @param any $data
     * @param integer $time in seconds
     * @return void
     */
    public static function set($name, $data, $time = 0)
    {
        Storage::save($name, $data, [ 'cache' => $time ]);
    }

    /**
     * Ritorna una variabile dalla cache
     *
     * @param string $name
     * @param any $callback valore di default
     * @param integer $cacheTimeCallback in seconds
     * @return void
     */
    public static function get($name, $callback, $cacheTimeCallback)
    {
        $file = Storage::get($name);
        $timestamp = $file ? time() - $file['timestamp'] : null;
        $cache = $file && isset($file['cache']) ? $file['cache'] : $cacheTimeCallback;

        if(!$timestamp || $timestamp > $cache) 
        {
            $value = is_callable($callback) ? $callback() : $callback;
            self::set($name, $value, $cache);
        } 
        else 
        {
            $value = $file['content'];
        }

        return $value;
    }
}