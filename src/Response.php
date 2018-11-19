<?php

namespace Desidus\Rudder;

class Response
{

    private static $isJson = false;


    public static function json($data)
    {
        self::$isJson = true;
        return $data;
    }

    public static function send($request, $data)
    {
        if($request->ajax || self::$isJson)
            header('Content-Type: application/json');
        else
            header('Content-Type: text/html');

        echo is_string($data) ? $data : json_encode($data);
    }

}

