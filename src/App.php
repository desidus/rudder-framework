<?php

namespace Desidus\Rudder;

class App
{
    private static $appPath;
    private static $request;

    /**
     * Setta la public e richiama le routes
     */
    public static function load($appPath)
    {
        self::$appPath = $appPath;

        $dotenv = new \Dotenv\Dotenv(self::$appPath);
        $dotenv->load();

        require_once Route::path();
        
        setlocale(LC_TIME, getenv('APP_LANG'));
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