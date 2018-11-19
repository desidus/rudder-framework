<?php

namespace Desidus\Rudder;

class Cache {

    public static function set($name, $data, $time = 0)
    {
        Storage::save($name, $data, [ 'cache' => $time ]);
    }

    public static function get($name, $callback, $cacheTimeCallback)
    {
        $file = Storage::get($name);
        $timestamp = $file ? time() - $file['timestamp'] : null;
        $cache = $file && isset($file['cache']) ? $file['cache'] : $cacheTimeCallback;

        if(!$timestamp || $timestamp > $cache) {
            $value = is_callable($callback) ? $callback() : $callback;
            self::set($name, $value, $cache);
        } else {
            $value = $file['content'];
        }

        return $value;
    }
}