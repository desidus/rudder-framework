<?php

namespace Desidus\Rudder;

class App
{
    private static $appPath;
    private static $publicPath;
    private static $request;

    /**
     * Setta la public e richiama le routes
     */
    public static function load($appPath, $publicPath)
    {
        self::$appPath = $appPath;
        self::$publicPath = $publicPath;

        $dotenv = new \Dotenv\Dotenv($publicPath);
        $dotenv->load();

        require_once Route::path();
    }

    /**
     * Risolve la richiesta
     */
    public static function handle($request)
    {
        self::$request = $request;
        
        $responseData = Route::resolve( $request );

        return Response::send( $request, $responseData );
    }

    /**
     * Ritorna il path publico
     */
     public static function publicPath() 
     {
         return self::$publicPath;
     }

     /**
      * Ritorna il path dell'applicazione
      */
     public static function appPath() 
     {
         return self::$appPath;
     }

     /**
      * get Request
      *
      * @return Aloe\Radish\Request
      */
     public static function request()
     {
         return self::$request;
     }
     
}