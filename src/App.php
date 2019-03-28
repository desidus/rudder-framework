<?php

namespace Desidus\Rudder;

class App
{
private static $appPath;
private static $request;
private static $debug;
private static $env;

/**
 * Setta la public e richiama le routes
 */
public static function load($appPath)
{
    self::$appPath = $appPath;
    
    $dotenv = new \Dotenv\Dotenv(self::$appPath);
    $dotenv->load();
    self::$debug = getenv('APP_DEBUG');
    self::$env = getenv('APP_ENV');

    require_once Route::path();
    
    setlocale(LC_TIME, getenv('APP_LANG'));
}

/**
 * Risolve la richiesta
 */
public static function handle($request)
{
    self::$request = $request;
    
    if ($error = Input::exceededRequestSize())
    {
        return Response::send(
            self::$debug ? ['error' => $error] : [], 
            Response::HTTP_REQUEST_ENTITY_TOO_LARGE
        );
    }

    $response = Route::resolve( $request );

    return $response instanceof Response ? $response->apply() : Response::send($response);
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
    
    public static function inProduction()
    {
        return self::$env == 'production';
    }
}