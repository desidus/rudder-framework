<?php

namespace Desidus\Rudder;

class Request
{

	private $method;
	private $ajax;
	private $url;
	private $uri;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
		$this->url = self::getCurrentUrl();
		$this->uri = self::getCurrentUri();
	}

	/**
	 * Get URI from Request
	 */
	public static function getCurrentUrl()
	{
		return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	public static function getCurrentUri()
	{
		$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		$uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
		if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
		return '/' . trim($uri, '/');
	}

	/**
	 * Public methods
	 */
	public function __get($name)
	{
        return $this->$name;
    }

}
