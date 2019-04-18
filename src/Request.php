<?php

namespace Desidus\Rudder;

class Request
{

	private $method;
	private $ajax;
	private $url;
	private $uri;
	private $uriParams;

	/**
	 * Constructor
	 */
	function __construct()
	{
		header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
		
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->uriParams = [];
		$this->ajax = 
			( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
			|| ( isset($_SERVER['HTTP_ACCEPT']) && preg_match('/application\/json/i', $_SERVER['HTTP_ACCEPT']) )
		;
		
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

	public function input($keys = [])
	{
		return is_string($keys) ? Input::get($keys) : Input::only($only);
	}
	
	public function hasFile($key)
	{
		return Input::hasFile($key);
	}

	public function wantJSON()
	{
		return $this->ajax;
	}

	public function setUriParams($params)
	{
		$this->uriParams = $params;
	}

	public function getUriParams()
	{
		return $this->uriParams;
	}

	/**
	 * Public methods
	 */
	public function __get($name)
	{
        return $this->$name;
    }

}
