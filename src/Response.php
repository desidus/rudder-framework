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
        $bDataString = is_string($data);
        
        if($request->ajax || self::$isJson || !$bDataString)
            header('Content-Type: application/json');
        else
            header('Content-Type: text/html');

        echo $bDataString ? $data : json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }

}

