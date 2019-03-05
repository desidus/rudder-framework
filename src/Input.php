<?php

namespace Desidus\Rudder;

class Input 
{
    private static function getRequestData()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $data = [];

        if ($method == 'GET')
            $data = $_GET;
        else 
            $data = count($_POST) > 0 ? $_POST : json_decode(file_get_contents('php://input'), true);
        
        return $data;
    }

    public static function get($key = null)
    {  
        $data = self::getRequestData();

        return isset($key) ? array_key_exists($key, $data) ? $data[$key] : null : $data;
    }
}
