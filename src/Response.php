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
        if (is_string($data))
        {
            header('Content-Type: text/html');
            echo $data;
        }
        else
        {
            header('Content-Type: application/json');
            echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        }
    }

}

