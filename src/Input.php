<?php

namespace Desidus\Rudder;

class Input 
{
    private static $data = null;

    /**
     * Setta tutti i dati in input della richiesta
     *
     * @return Array
     */
    private static function getRequestData()
    {
        if (!self::$data)
        {
            $method = $_SERVER['REQUEST_METHOD'];

            switch($method)
            {
                case "GET":
                    self::$data = $_GET;
                    break;
                case "POST":
                    self::$data = count($_POST) > 0 ? $_POST : json_decode(file_get_contents('php://input'), true);
                    if (count($_FILES) > 0) 
                    {
                        self::$data = array_merge(self::$data, $_FILES);
                    }
                    break;
            }
        }
        
        self::$data = self::$data == null ? [] : self::$data;

        return self::$data;
    }

    /**
     * Ritorna tutti i dati della richiesta
     *
     * @return Array
     */
    public static function all()
    {
        return self::getRequestData();
    }

    /**
     * Ritorna solo i dati in input specificati
     *
     * @param Array $keys
     * @return Array
     */
    public static function only($keys)
    {
        $data = self::all();
        if (count($keys) <= 0) 
        {
            return $data;
        }

        return array_intersect_key($data, array_flip((array) $keys));
    }

    public static function get($key = null)
    {  
        $data = self::getRequestData();

        return isset($key) ? array_key_exists($key, $data) ? $data[$key] : null : $data;
    }

    public static function hasFile($key)
    {
        return array_key_exists($key, $_FILES) && is_uploaded_file($_FILES[$key]['tmp_name']);
    }

    public static function exceededRequestSize()
    {
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0 )
        {      
            $displayMaxSize = ini_get('post_max_size');
        
            switch (substr($displayMaxSize, -1))
            {
                case 'G':
                    $displayMaxSize = intval($displayMaxSize) * 1024;
                case 'M':
                    $displayMaxSize = intval($displayMaxSize) * 1024;
                case 'K':
                    $displayMaxSize = intval($displayMaxSize) * 1024;
            }
            
            return 'Posted data is too large. '. $_SERVER['CONTENT_LENGTH']. ' bytes exceeds the maximum size of '. $displayMaxSize.' bytes.';
        }

        return false;
    }
}
