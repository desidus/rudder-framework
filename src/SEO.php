<?php

namespace Desidus\Rudder;

class SEO
{
    /**
     * seo config path
     * @var String
     */
    public static $path = 'seo.php';
    
    /**
     * default Microdata 
     */
    public static $defaultRoutingSearch = '/';

    /**
     * Ritorna i dati da una richiesta
     */
    public static function resolve()
    {
        $uri = Request::getCurrentURI();
        $seo = require App::appPath() . '/' . self::$path;

        foreach ($seo as $regexp => $callback) 
        {
            if (preg_match($regexp, '')) 
            {
                if ($regexp == $uri) 
                {
                    return $callback();
                }
            } 
            else 
            {
                $matches = [];
                preg_match($regexp, $uri, $matches, PREG_OFFSET_CAPTURE);
                
                if (count($matches) && !empty($matches[1][0])) 
                {
                    return $callback($matches[1][0]);
                }
            }
        }

        if (array_key_exists(self::$defaultRoutingSearch, $seo))
            return $seo[self::$defaultRoutingSearch]();

        return [];
    }

    

}