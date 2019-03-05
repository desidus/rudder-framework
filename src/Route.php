<?php

namespace Desidus\Rudder;

class Route
{
    /**
     * Route path
     * @var String
     */
    public static $path = 'routes.php';

    /**
     * Missing method
     */
    private static $missingCallback;

    /**
     * Azioni di routing
     * @var Array
     */
    private static $actions = [
        'GET' => [],
        'POST' => []
    ];

    /**
     * Middlewares definiti
     * @var Array
     */
    private static $middlewares = [];

    /**
     * path delle routes
     */
    public static function path()
    {
        return App::appPath() . '/' . self::$path;
    }

    /**
     * Ritorna i dati da una richiesta
     */
    public static function resolve($request)
    {
        self::resolveMiddlewares($request);

        $method = $request->method;
        $uri = $request->uri;
        $action = array_key_exists($uri, self::$actions[$method]) ? self::$actions[$method][$uri] : null;

        if($action === null)
            return ($callback = self::$missingCallback) ? $callback($request) : null;

        if(gettype($action) == 'string')
            return self::resolveClass('App\\Controllers\\' . $action, $request);

        return $action($request);
    }

    /**
     * Applica i middlewares
     */
    public static function resolveMiddlewares($request)
    {
        foreach (self::$middlewares as $middleware) 
        {
            $checked = false;
            if(is_string($middleware['check']))
            {
                $class = 'App\\Middlewares\\' . $middleware['check'];
                $class = new $class();
                $checked = $class->handle($request);
            }
            else 
            {
                $checked = $middleware['check']($request);
            }
            
            if ($checked) 
            {
                $middleware['callback']();
            }
        }  
    }

    /**
     * Resolve class
     */
    protected static function resolveClass($class, $request)
    {
        $class = explode('@', $class);

        $method = $class[1];
        $class = $class[0];

        $class = new $class();
        return $class->$method($request);
    }

    /**
     * Missing method
     */
    public static function missing($resolve)
    {
        self::$missingCallback = $resolve;
    }

    /**
     * Setto un'action di tipo GET
     */
    public static function get($uri, $resolve)
    {
        self::addAction('GET', $uri, $resolve);
    }

    /**
     * Setto un'action di tipo POST
     */
    public static function post($uri, $resolve)
    {
        self::addAction('POST', $uri, $resolve);
    }

    /**
     * Setto un'action di tipo POST
     */
    public static function middleware($middleware, $callback)
    {
        array_push(self::$middlewares, [
            'check' => $middleware, 
            'callback' => $callback
        ]);
    }

    /**
     * Setto un'action di tipo POST
     */
    private static function addAction($method, $uri, $resolve)
    {
        $uri = '/' . trim($uri, '/');

        self::$actions[$method][$uri] = $resolve;
    }

}